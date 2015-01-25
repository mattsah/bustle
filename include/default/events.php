<?php

	return Affinity\Action::create(['core'], function($app, $broker) {
		$app['events'] = $broker->make('Inkwell\Event\Manager');

		$broker->prepare('Inkwell\Event\EmitterInterface', function($emitter) use ($app) {
			$app['events']->watch($emitter);
		});
	});