<?php

	return Affinity\Config::create(['providers'], [
		'@providers' => [
			'params' => [
				'Inkwell\Security\JWTCookieWrapper' => [
					':key' => 'Replace this key with a secret key'
				],

				'Inkwell\HTTP\Gateway\Server' => [
					'cookie_wrapper' => 'Inkwell\Security\JWTCookieWrapper'
				],
			]
		]
	]);
