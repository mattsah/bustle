<% namespace Inkwell\HTML
{
	$this->expand('content', 'layouts/full.html');
	$this->assign('header',  'common/header.html');

	%>
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
				<div id="monday" class="day" data-date="">
					<h3>Monday</h3>
					<form method="post">
						<input type="text" name="title" />
						<button type="submit">Go</button>
					</form>
					<ul>

					</ul>
				</div>

				<div class="day" data-date="">
					<h3>Tuesday</h3>
					<form method="post">
						<input type="text" name="title" />
						<button type="submit">Go</button>
					</form>
					<ul>

					</ul>
				</div>

				<div class="day" data-date="">
					<h3>Wednesday</h3>
					<form method="post">
						<input type="text" name="title" />
						<button type="submit">Go</button>
					</form>
					<ul>

					</ul>
				</div>

				<div class="day" data-date="">
					<h3>Thursday</h3>
					<form method="post">
						<input type="text" name="title" />
						<button type="submit">Go</button>
					</form>
					<ul>

					</ul>
				</div>

				<div class="day" data-date="">
					<h3>Friday</h3>
					<form method="post">
						<input type="text" name="title" />
						<button type="submit">Go</button>
					</form>
					<ul>

					</ul>
				</div>

			</div>
		</section>
	<%
}
