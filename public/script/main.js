require([
	'dojo/on',
	'dojo/query',
	'dojo/dom-class',
	'dojo/dom-construct',
	'dojo/_base/event',
	'local/sortable',
	'dojo/domReady!'
], function (on, query, style, elem, event, Sortable) {

	query('.tasks ul').forEach(function(list) {
		list.sortable = new Sortable(list, {
			group: 'tasks',
			sort: true
		});
	});

	query('.tasks form').on('submit', function(e) {
		elem.place('<li>' + this.title.value + '<a href="" class="close" title="Click here to mark this task complete.">✖</a></li>', query('ul', this.parentNode)[0], 'last');
		this.title.value = '';
		event.stop(e);
	});

	query('.queue .toggle').on('click', function(e) {
		style.toggle(this.parentNode, 'active');
	});

	query('.queue').on('dragenter, dragleave', function(e) {
		console.log(e.clientX);
		console.log(this);
		if (e.type == 'dragenter') {
			style.add(this, 'active');
		} else if (e.clientX >= this.clientWidth) {
			style.remove(this, 'active');
		}
	});
});
