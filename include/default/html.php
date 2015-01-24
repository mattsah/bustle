<?php

	use Inkwell\HTML;

	return Affinity\Action::create(function($app, $broker) {

		HTML\html::add([
			'money' => new HTML\money(
				$app['engine']->fetch('html', 'money.currency',  '$'),
				$app['engine']->fetch('html', 'money.decimal',   '.'),
				$app['engine']->fetch('html', 'money.separator', ',')
			)
		]);

		$broker->prepare('Inkwell\View', function($view) {
			$view->filter('html', ['Inkwell\HTML\html', 'out']);
		});
	});
