<?php

	use Whoops\Handler\PrettyPageHandler;
	use Whoops\Handler\JsonResponseHandler;

	return Affinity\Action::create(function($app, $broker) {

		//
		// Setup execution mode and debugging
		//

		if (!$app->checkSAPI(['cli', 'embed'])) {
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
		}

		$server_admin      = $app->getEnvironment('SERVER_ADMIN', 'root');
		$debugging         = $app['engine']->fetch('core', 'debugging', []);
		$execution_mode    = $app['engine']->fetch('core', 'execution_mode', IW\EXEC_MODE\PRODUCTION);

		if (!isset($debugging['destination'])) {
			$debugging['destination'] = $execution_mode != IW\EXEC_MODE\PRODUCTION
				? IW\ERROR\DESTINATION_RESPONSE
				: IW\ERROR\DESTINATION_NULL;
		}

		if ($debugging['destination'] != IW\ERROR\DESTINATION_NULL) {
			$debug_manager = new Whoops\Run;

			switch ($debugging['destination']) {
				case IW\ERROR\DESTINATION_RESPONSE:
					$debug_handler = new PrettyPageHandler();
					$debug_handler->setPageTitle("Whoops! There was a problem.");
					$debug_manager->pushHandler($debug_handler);
					break;

				default:
					$debug_manager->pushHandler(function($exception, $inspector, $manager){
						//
						// @todo E-mail
						//
					});
					break;
			}

			$debug_manager->register();
		}

		$app->setExecutionMode($execution_mode);
		$app->setWriteDirectory($app['engine']->fetch('core', 'write_directory', 'writable'));

		date_default_timezone_set($app['engine']->fetch('core', 'timezone', 'GMT'));

		foreach ($app['engine']->fetch('@providers') as $id) {
			$provider_mapping = $app['engine']->fetch($id, '@providers.mapping', []);
			$provider_params  = $app['engine']->fetch($id, '@providers.params',  []);

			foreach ($provider_mapping as $interface => $provider) {
				$broker->alias($interface, $provider);
			}

			foreach ($provider_params as $provider => $params) {
				$broker->define($provider, $params);
			}
		}

		//
		// Make our container a shared instance for itself so we maintain all of the above
		//

		$broker->share($broker);
	});
