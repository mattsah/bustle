<?php namespace Inkwell\HTML;

	$this->expand('content', 'layouts/full.html');

	?>
	<form class="account join" method="post" action="">
		<?php $this->inject('messaging.html') ?>

		<label>Name</label>
		<input type="text" name="name" />

		<label>Email</label>
		<input type="text" name="email" />

		<button type="submit">Join</button>

		<p>
			Already have an account? <a href="/login">Login here</a>.
		</p>
	</form>
