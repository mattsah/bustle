<?php

	return Affinity\Config::create([
		'connections' => [
			'default' => [
				'adapter'    => 'pgsql',
				'dsn'        => 'pgsql:host=localhost;dbname=bustle',
				'user'       => 'web',
				'password'   => NULL,
				'attributes' => [],
				'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
			]
		]
	]);
