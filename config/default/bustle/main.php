<?php

	use IW\HTTP;

	return Affinity\Config::create(['providers', 'routes', 'auth'], [
		'@routes' => [
			'links' => [
				'/'           => 'MainController::home',
				'/[$:method]' => 'MainController::[lc:method]'
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
