<?php

	return Affinity\Action::create(['core'], function($app, $container) {
		$app['events'] = $container->make('Inkwell\Event\Manager');

		$container->prepare('Inkwell\Event\EmitterInterface', function($emitter) use ($app) {
			$app['events']->watch($emitter);
		});
	});
