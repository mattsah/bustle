# HTML Helper

## Installation

```bash
composer require dotink/inkwell-html
```

### Overview

The HTML helper provides a facade class and a number of fly-weight classes which are invoked to
perform particular functionality.  It is best consumed with a view object which will automatically
determine the template type and wrap accessed data using it, but it can be used independently as
well.

If you're using the [official inKWell view component](../responding/01-views), then the plugin
action provided by the html component will do this for you by registering a filter on views which
are dependency injected, see `config/default/html.php`:

```php
$broker->prepare('Inkwell\View', function($view) {
	$view->filter('html', ['Inkwell\HTML\html', 'out']);
});
```

If you're not using that view component, you can either use the `out()` method directly or enable
your view's data access methods to wrap your data with it.

In either case, you will likely want to make sure your templates are namespaced as follows, as this
is the namespace of the `html` facade:

```php
namespace Inkwell\HTML;
```

### Outputting

Outputting values to HTML:

```html
<?= html::out('This & that') ?>
```

If no filters are set, the default action is to escape values, so the output of the above would be
`This &amp; that`.

### Iterating

```html
<?php html::per([1, 2, 3], function($i, $val){ ?>
	I see the number <?= $val ?><br />
<?php }) ?>
```

This will output:

```html
I see the number 1<br/>
I see the number 2<br/>
I see the number 3<br/>
```

<div class="notice">
	<p>
		In the above example both the `$i` and `$val` values will be wrapped in `html::out()`
		before being passed to the closure.  This will apply any filters, including automatic
		escaping to these values.
	</p>
</div>

### Raw

The `raw()` filter can be used to unescape HTML entities or prevent automatic escaping for a
single value:

```php
<?= html::raw('This & that') ?>
```

This will output `This & that` as shown instead of escaping to `This &amp; that`.

You can also apply this to any values passed through `html::out()` for an entire block of HTML by
using a closure:

```html
<?php html::raw(function() { ?>
	<h3>Current Rich Text Body</h3>
	<div class="body">
		<?= html::out($content) ?>
	</div>
<?php }) ?>
```

### Money

You can use the `money()` to output monetary values.

```html
<?= html::money(2) ?>
```

Will output `$2.00` by default.  Unlike some simpler filters, money can be configured by changing
the values in `config/default/html.php` or by overloading them in a location specific
configuration:

```php
'money' => [
	'currency'  => '$',
	'decimal'   => '.',
	'separator' => ','
]
```

These are configured via the `config/default/html.php` provided action:

```php
HTML\html::add([
	'money' => new HTML\money(
		$app['engine']->fetch('html', 'money.currency', '$'),
		$app['engine']->fetch('html', 'money.decimal',  '.'),
		$app['engine']->fetch('html', 'money.separator', ',')
	)
]);
```
