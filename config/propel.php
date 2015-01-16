<?php

	return [
		'propel' => [
			'paths' => [
				'phpDir' => 'user/models'
			],

			'database' => [
				'connections' => [
					'bustle' => [
						'adapter'    => 'pgsql',
						'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
						'dsn'        => 'pgsql:host=localhost;dbname=bustle',
						'user'       => 'web',
						'password'   => NULL,
						'attributes' => []
					]
				]
			],

			'runtime' => [
				'defaultConnection' => 'bustle',
				'connections' => ['bustle']
			],

			'generator' => [
				'defaultConnection' => 'bustle',
				'connections' => ['bustle']
			]
		]
	];
