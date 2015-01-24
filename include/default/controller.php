<?php

	return Affinity\Action::create(['core', 'routing'], function($app, $broker) {
		$app['router.resolver'] = $broker->make('Inkwell\Routing\ResolverInterface');
	});
