<?php

	return Affinity\Action::create(['core'], function($app, $resolver) {
		$root_directory = $app['engine']->fetch('view', 'root_directory', 'user/templates');

		$resolver->define('Inkwell\View', [
			':root_directory' => $app->getDirectory($root_directory)
		]);
	});
