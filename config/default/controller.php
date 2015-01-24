<?php

	use IW\HTTP;

	return Affinity\Config::create(['providers'], [
		'@providers' => [

			//
			// The provider mapping lists concrete class providers for given interfaces, the
			// interface is the key, while the class is the value.
			//

			'mapping' => [
				'Inkwell\Routing\ResolverInterface' => 'Inkwell\Controller\Resolver',
			]
		],
	]);
