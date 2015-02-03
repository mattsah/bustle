<?php namespace Inkwell\HTML;

	$this->expand('content', 'layouts/full.html');

	?>
	<section class="tasks" role="main">
		<div class="queue">
			<h2>
				Available Tasks
			</h2>
			<form method="post">
				<input type="text" name="title" />
				<button type="submit">Go</button>
			</form>
			<ul>

			</ul>
			<a class="toggle" title="Toggle the available tasks queue">≡</a>
		</div>

		<div>
			<?php html::per($this('days'), function($i, $day) { ?>
				<div id="<?= html::lower($day->format('l')) ?>" class="day" data-date="<?= html::out($day->format('Y-m-d')) ?>">
					<h3><?= html::out($day->format('l')) ?></h3>
					<form method="post">
						<input type="text" name="title" />
						<button type="submit">Go</button>
					</form>
					<ul>

					</ul>
				</div>
			<?php }) ?>
		</div>
	</section>
