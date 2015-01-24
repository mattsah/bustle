<?php

	use Propel\Runtime\Propel;
	use Propel\Runtime\Connection\ConnectionManagerSingle;

	return Affinity\Action::create(['core'], function($app, $broker) {
		$container = Propel::getServiceContainer();
		$manager   = new ConnectionManagerSingle();

		//
		// Runtime Config Here
		//
	});
