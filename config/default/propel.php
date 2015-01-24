<?php

	return Affinity\Config::create([
		'paths' => [
			'phpDir' => 'user/models'
		],

		'runtime' => [
			'defaultConnection' => 'default',
			'connections' => ['default']
		],

		'generator' => [
			'defaultConnection' => 'default',
			'connections' => ['default']
		]
	]);
