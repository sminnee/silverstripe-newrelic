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
}