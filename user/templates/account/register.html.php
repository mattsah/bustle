<?php namespace Inkwell\HTML;

	$this->expand('content', 'layouts/full.html');

	?>
	<form class="account join" method="post" action="">
		<?php $this->inject('messaging.html') ?>

		<label>Name</label>
		<input type="text" name="name" value="<?= $this('name') ?>" />

		<label>Full Name</label>
		<input type="text" name="full_name" value="<?= $this('full_name') ?>" />

		<label>Password</label>
		<input type="password" name="password" value="" />

		<label>Email</label>
		<input type="text" name="email" value="<?= $this('email') ?>" disabled="true" />

		<button type="submit">Register</button>

		<p>
			Don't want to use this e-mail?  <a href="/join">Create a new join request</a>.
		</p>
	</form>
