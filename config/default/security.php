<?php

	use IW\HTTP;

	return Affinity\Config::create(['providers', 'routes'], [
		'@providers' => [

			//
			// The UserProviderInterface can be set to an implementation that is specific for
			// your system.  You will need to set this before the auth controller performs any
			// actions since it depends on the user provider to interface with user accounts.
			//
			// You should probably actually copy this and register it at the app level.  This is
			// more just for an example.
			//

			'mapping' => [
				'Inkwell\Security\UserProviderInterface' => 'UserProvider'
			],

			//
			// Settings for our cookie wrapper.  You can set a custom key by setting a `CW_KEY`
			// environment variable.
			//

			'params' => [
				'Inkwell\Security\JWTCookieWrapper' => [
					':key' => $app->getEnvironment('CW_KEY', 'Replace this key with a secret key')
				],

				'Inkwell\HTTP\Gateway\Server' => [
					'cookie_wrapper' => 'Inkwell\Security\JWTCookieWrapper'
				],
			]
		],

		//
		// Default routes and error handling
		//

		'@routes' => [
			'links' => [
				'/[(login|logout|join|register|reset):method]' => 'Inkwell\Security\AccountController::[lc:method]',
			],

			'handlers' => [
				HTTP\FORBIDDEN => 'Inkwell\Security\AccountController::forbidden'
			]
		]
	]);
