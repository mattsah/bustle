<?php

	use IW\HTTP;

	return Affinity\Config::create(['providers', 'routes', 'auth'], [
		'@providers' => [
			'mapping' => [
				'Inkwell\Security\UserProviderInterface' => 'UserProvider'
			]
		],

		'@routes' => [
			'links' => [
				'/'                                      => 'MainController::home',
				'/[$:method]'                            => 'MainController::[lc:method]',
				'/[(login|logout|join|register):method]' => 'Inkwell\Security\AccountController::[lc:method]',
			],

			'handlers' => [
				HTTP\FORBIDDEN => 'Inkwell\Security\AccountController::forbidden'
			]
		],

		'@auth' => [
			'permissions' => [
				'Admin' => [
					'task' => ['read']
				]
			]
		]
	]);
