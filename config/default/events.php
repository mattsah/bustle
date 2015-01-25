<?php

	return Affinity\Config::create(['providers'], [

		//
		// @providers allows you to wire together dependencies
		//

		'@providers' => [

			//
			// The provider mapping lists concrete class providers for given interfaces, the
			// interface is the key, while the class is the value.
			//

			'mapping' => [
				'Inkwell\Event\ManagerInterface' => 'Inkwell\Event\Manager'
			]
		]
	]);