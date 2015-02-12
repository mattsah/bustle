<?php

	return Affinity\Action::create(['core', 'routing'], function($app, $broker) {
		$app['router.resolver'] = $broker->make('Inkwell\Routing\ResolverInterface', [
			':broker' => $broker
		]);

		$broker->prepare('Inkwell\Controller\NegotiatorConsumerInterface', function($negotiator, $broker) {
			$language_negotiator = $broker->make('Negotiation\LanguageNegotiator');
			$mimetype_negotiator = $broker->make('Negotiation\FormatNegotiator');

			$negotiator->setLanguageNegotiator($language_negotiator);
			$negotiator->setMimeTypeNegotiator($mimetype_negotiator);
		});
	});
