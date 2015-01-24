<?php

	return Affinity\Config::create([
		'connections' => [
			'default' => [
				'adapter'    => NULL,
				'dsn'        => NULL,
				'user'       => NULL,
				'password'   => NULL,
				'attributes' => [],
				'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
			]
		]
	]);
