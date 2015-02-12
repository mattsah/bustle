<?php
	use Dotink\Flourish;

	return Affinity\Action::create(['core', 'auth', 'controller'], function($app, $broker) {
		try {
			//
			// We're going to try to make our user provider straight away.  If that doesn't work
			// we'll return immediately.
			//

			$user_provider = $broker->make('Inkwell\Security\UserProviderInterface');

		} catch (Auryn\InjectionException $e) {
			return;
		}

		//
		// Now we want to define our user provider for our account controller.  If it's not
		// defined, the account controller will set 404 and defer.
		//
		$broker->define('Inkwell\Security\AccountController', [
				':user_provider' => $user_provider
		]);


		//
		// Our login action will run at the beginning of our routing
		//
		$app['events']->on('Router::begin', function($event, $data, $context) use ($app) {
			$login = $app['router.resolver']->resolve(
				'Inkwell\Security\AccountController::login', [
						'router'   => $context,
						'request'  => $data['request'],
						'response' => $data['response']
				]
			);

			$app['auth.init']($login());
		});

		//
		// The refresh will run on the way out
		//
		$app['events']->on('Router::end', function($event, $data, $context) use ($app) {
			$refresh = $refresh_action = $app['router.resolver']->resolve(
				'Inkwell\Security\AccountController::refresh', [
					'router'   => $context,
					'request'  => $data['request'],
					'response' => $data['response']
				]
			);

			$refresh();
		});
	});
