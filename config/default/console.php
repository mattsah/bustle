<?php

	return Affinity\Config::create(['quill'], [
			'@quill' => [
					'commands' => [
						'Inkwell\Console\CdCommand',
						'Inkwell\Console\LsCommand'
					]
			]
	]);
