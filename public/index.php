<?php namespace Inkwell
{
	use Exception;
	use Closure;
	use IW;

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	try {

		call_user_func(function() {

			//
			// Track backwards until we discover our includes directory.  The only file required
			// to be in place for this is includes/init.php which should return our application
			// instance.
			//

			for (

				//
				// Initial assignment
				//

				$init_path = 'init.php';

				//
				// While Condition
				//

				!is_file($init_path);

				//
				// Modifier
				//

				$init_path = realpath('..' . DIRECTORY_SEPARATOR . $init_path)
			);

			$app = include($init_path);

			$app->run(function($app, $container) {
				$request  = $container->make('Inkwell\HTTP\Resource\Request');
				$gateway  = $container->make('Inkwell\HTTP\Gateway\Server');

				$gateway->populate($request);

				$resolver = $container->make('Inkwell\Routing\ResolverInterface');
				$response = $app['router']->run($request, $resolver);

				$gateway->transport($response);
			});
		});

	} catch (Exception $e) {

		//
		// Panic here, attempt to determine what state we're in, see if some errors handlers are
		// callable or if we're totally fucked.  In the end, throw the exception and be damned.
		//

		throw $e;

		exit(0);
	}
}
