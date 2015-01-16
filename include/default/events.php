<?php

	return Affinity\Action::create(['core'], function($app, $resolver) {
		$app['events'] = $resolver->make('Inkwell\Event\Manager');

		$resolver->prepare('Inkwell\Event\EmitterInterface', function($emitter) use ($app) {
			$app['events']->watch($emitter);
		});
	});
