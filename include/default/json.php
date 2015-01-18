<?php

	return Affinity\Action::create(['core', 'events'], function($app, $resolver) {
		$app['events']->on('Router::actionComplete', function($action, $context) {
			$response = $context['response'];

			if ($response->headers->get('Content-Type') == 'application/json') {
					$response->set(json_encode($response->get()));
			}
		});
	});
