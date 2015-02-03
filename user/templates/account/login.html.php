<?php namespace Inkwell\HTML;

	$this->expand('content', 'layouts/full.html');

	?>
	<form class="account login" method="post" action="">
		<?= $this('error')->exists() ? $this('error')->retrieve()->compose() : NULL ?>

		<label>Login</label>
		<input type="text" name="login" />

		<label>Password</label>
		<input type="password" name="password" />

		<button type="submit">Login</button>

		<p>
			Don't have an account? <a href="/join">Join here</a>.
		</p>


	</form>
