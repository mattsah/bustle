# HTML Helper

## Installation

```bash
composer require dotink/inkwell-html
```

## Instantiation

The HTML helper provides a facade class and a number of fly-weight classes which are invoked to
perform particular functionality.  It is best consumed with a view object which will automatically
determine the template type and wrap accessed data using it, but it can be used independently as
well.

If you're using the [official inKWell view component](../responding/01-views), then the plugin
action provided by the html component will do this for you by registering a filter on views which
are dependency injected.

If you're not using that view component, you can either use the `html::out()` method directly or
enable your view's data access methods to wrap your data with it.

There is no instantiation, it's just a facade.

## Usage

You will likely want to make sure your templates are namespaced with `Inkwell\HTML` to prevent the
need for verbose class references.  This is the namespace of the `html` facade:

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
<?php })) ?>
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

Will output `$2.00` by default.  Unlike some simpler filters, money can be configured.  You can
configure it by performing the following prior to use:

```php
Inkwell\HTML\html::add([
	'money' => new Inkwell\HTML\money($currency, $decimal, $separator)
]);
```
