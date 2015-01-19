<?php

	return Affinity\Action::create(['core'], function($app, $container) {
		foreach ($app['engine']->fetch('response', 'states') as $status => $data) {
			if (isset($data['code'])) {
				Inkwell\HTTP\Resource\Response::addCode($status, $data['code']);
			}

			if (isset($data['body'])) {
				Inkwell\HTTP\Resource\Response::addMessage($status, $data['body']);
			}
		}

		Inkwell\HTTP\Resource\Response::setDefaultStatus(
			$app['engine']->fetch('response', 'default_status', IW\HTTP\NOT_FOUND)
		);
	});
