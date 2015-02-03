<?php namespace Inkwell\HTML;

	$this->expand('content', 'layouts/full.html');

	?>
	<form class="account join" method="post" action="">
		<?= $this('success')->exists() ? $this('success')->retrieve()->compose() : NULL ?>
		<?= $this('error')->exists()   ? $this('error')->retrieve()->compose()   : NULL?>

		<label>Name</label>
		<input type="text" name="name" />

		<label>Email</label>
		<input type="text" name="email" />

		<button type="submit">Join</button>

		<p>
			Already have an account? <a href="/login">Login here</a>.
		</p>
	</form>
