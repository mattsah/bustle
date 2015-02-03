<?php
	use Dotink\Flourish;

	return Affinity\Action::create(['core', 'auth', 'routing'], function($app, $broker) {
		try {
			$auth_controller = $broker->make('Inkwell\Security\AccountController');
			$auth_callback   = [$auth_controller, 'login'];

			$auth_controller->__prepare('login', [
				'router'   => $app['router'],
				'request'  => $app['request'],
				'response' => $app['response']
			]);

			$app['auth.init']($auth_callback());

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
