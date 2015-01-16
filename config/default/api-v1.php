<?php

	return Affinity\Config::create(['routes'], [

		//
		// API
		//

		'@routes' => [
			'base_url' => '/api/v1.0/',
			'links'    => [
				'[!:collection]/'           => 'API\ResourceController::handleCollection',
				'[!:collection]/[!:entity]' => 'API\ResourceController::handleEntity'
			]
		]
	]);
