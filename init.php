<?php namespace Inkwell
{
	use Affinity;
	use Auryn;

	@include 'vendor/autoload.php';
	@include 'constants.php';

	$app  	       = new Core(realpath(__DIR__));
	$broker        = new Auryn\Provider();
	$config_dir    = $app->getDirectory($app->getEnvironment('IW_CONFIG_ROOT', 'config'));
	$action_dir    = $app->getDirectory($app->getEnvironment('IW_ACTION_ROOT', 'include'));
	$environment   = $app->getEnvironment('IW_ENVIRONMENT', 'prod');

	$app['engine'] = new Affinity\Engine(
		new Affinity\NativeDriver($config_dir),
		new Affinity\NativeDriver($action_dir)
	);

	$app['engine']->start($environment, $app, $broker);

	return $app;
}
