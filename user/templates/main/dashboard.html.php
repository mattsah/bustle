<?php namespace Inkwell\HTML;

	$this->expand('content', 'layouts/full.html');

	?>
	<section class="tasks" role="main">
		<div class="queue">
			<h2>
				Available Tasks
			</h2>
			<form method="post" action="/api/v1/tasks/">
				<input type="text" name="title" />
				<button type="submit">Go</button>
			</form>
			<ul>

			</ul>
			<a class="toggle" title="Toggle the available tasks queue">≡</a>
		</div>

		<div>
			<?php html::per($this("days"), function($i, $day) { ?>
				<div id="<?= html::lower($day->format('l')) ?>" class="day" data-date="<?= $day->format('Y-m-d') ?>">
					<h3><?= $day->format('l') ?></h3>
					<form method="post" action="/api/v1/tasks/">
						<input type="text"   name="title" />
						<input type="hidden" name="startDate" value="<?= $day->format('Y-m-d') ?>" />
						<input type="hidden" name="assignee" value="0" />
						<button type="submit">Go</button>
					</form>
					<ul>
						<?php html::per($this("tasks.{$day->format('U')}") ?: array(), function($i, $task) { ?>
							<li>
								<?= html::out($task->getTitle()) ?>
								<a class="close" href="/tasks/<?= html::out($task->getId()) ?>">✖</a>
							</li>
						<?php }) ?>
					</ul>
				</div>
			<?php }) ?>
		</div>
	</section>
