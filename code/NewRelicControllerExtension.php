<?php

class NewRelicControllerExtension extends Extension
{

    protected $transactionName = null;

    public function onAfterInit()
    {
        $controller = get_class($this->owner);

        if ($this->owner->getResponse()->isFinished()) {
            $this->transactionName = "$controller/init-only";
        } else {
            $action = $this->owner->getRequest()->latestParam('Action');
            if (!$action) {
                $action = 'index';
            }
            $this->transactionName = "$controller/$action";
        }
    }

    public function beforeCallActionHandler($request, $action)
    {
        $controller = get_class($this->owner);
        $this->transactionName = "$controller/$action";
    }

    public function __destruct()
    {
        if ($this->transactionName) {
            //Debug::message("newrelic_name_transaction($this->transactionName)");
            if (extension_loaded('newrelic')) {
                newrelic_name_transaction($this->transactionName);
            }

            if ($memberID = Session::get('loggedInAs')) {
                //Debug::message("newrelic_add_custom_parameter('memberID', $memberID)");
                if (extension_loaded('newrelic')) {
                    newrelic_add_custom_parameter('memberID', $memberID);
                }
            }
        }
    }
}

// Set-up calls
if (extension_loaded('newrelic')) {
    //newrelic_add_custom_tracer("Director::direct,SSViewer::process");

    // Set the name to the vhost for client-side scripts
    // To do: get robust way of picking the canoncial hostname for www requests too
    if (Director::is_cli() && isset($_SERVER['HTTP_HOST'])) {
        newrelic_set_appname($_SERVER['HTTP_HOST']);
    }

    // Distinguish background from web requests more reliably
    newrelic_background_job(Director::is_cli());
}
