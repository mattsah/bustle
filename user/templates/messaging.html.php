<?php namespace Inkwell\HTML;

	$formatter = function($name, $message) {
		?><div class="<?= $name ?>"><p><?= $message ?></p></div><?php
	};

	html::per(['success', 'error', 'info'], function($i, $name) use ($formatter) {
		$message = $this['message']->create($name);

		if ($message->exists()) {
			echo $message->retrieve()->compose($formatter);
		}
	});
