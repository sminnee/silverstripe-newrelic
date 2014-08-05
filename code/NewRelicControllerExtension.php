<?php

class NewRelicControllerExtension extends Extension {

	function onAfterInit() {
		if (extension_loaded('newrelic')) {
			if($this->owner->getResponse()->isFinished()) {
				$controller = get_class($this->owner);
				newrelic_name_transaction("$controller/init");
			}

			if($memberID = Session::get('memberID')) {
				newrelic_add_custom_parameter('memberID', $memberID);
			}

		}
	}
	function beforeCallActionHandler($request, $action) {
		if (extension_loaded('newrelic')) {
			$controller = get_class($this->owner);
			newrelic_name_transaction("$controller/$action");
		}
	}
}

// Set-up calls
if (extension_loaded('newrelic')) {
	newrelic_add_custom_tracer("Director::direct,SSViewer::process");

	// Set the name to the vhost
	// To do: make this the canonical vhost, defined by $_FILE_TO_URL_MAPPING
	if(isset($_SERVER['HTTP_HOST'])) newrelic_set_appname($_SERVER['HTTP_HOST']);

	// Distinguish background from web requests more reliably
	newrelic_background_job(Director::is_cli());

}
