<?php

	use Propel\Runtime\Propel;
	use Propel\Runtime\Connection\ConnectionManagerSingle;

	return Affinity\Action::create(['core'], function($app, $broker) {
		$container   = Propel::getServiceContainer();
		$connections = $app['engine']->fetch('database', 'connections', array());

		foreach ($connections as $name => $config) {
			if (!isset($config['adapter'])) {
				continue;
			}

			$manager = new ConnectionManagerSingle();

			$manager->setConfiguration($config);
			$container->setAdapterClass($name, $config['adapter']);
			$container->setConnectionManager($name, $manager);
		}
	});
