<?php

	use Propel\Runtime\Propel;
	use Propel\Runtime\Connection\ConnectionManagerSingle;

	return Affinity\Action::create(['core'], function($app, $resolver) {
		$container = Propel::getServiceContainer();
		$manager   = new ConnectionManagerSingle();

		$manager->setConfiguration([
			'dsn'      => 'pgsql:host=localhost;dbname=bustle',
			'user'     => 'web',
			'password' => NULL
		]);

		$container->setAdapterClass('bustle', 'pgsql');
		$container->setConnectionManager('bustle', $manager);
	});
