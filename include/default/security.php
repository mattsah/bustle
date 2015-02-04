<?php
	use Dotink\Flourish;

	return Affinity\Action::create(['core', 'auth', 'routing'], function($app, $broker) {
		try {
			$controller = $broker->make('Inkwell\Security\AccountController');

			$controller->__prepare('login', [
				'router'   => $app['router'],
				'request'  => $app['request'],
				'response' => $app['response']
			]);

			$app['events']->on('Router::begin', function($event) use ($app, $controller) {
				$app['auth.init']($controller->login());
			});

			$app['events']->on('Router::end', function($event) use ($app, $controller) {
				$controller->refresh();
			});

		} catch (Auryn\InjectionException $e) {

			//
			// Code 3 occurs if you establish a provider but the class you provide cannot be
			// found.
			//

			if ($e->getCode() == 3) {
					throw new Flourish\ProgrammerException($e->getMessage());
			}
		}
	});
