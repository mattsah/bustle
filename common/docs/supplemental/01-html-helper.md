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
need for verbose class references.  You can do this by ensuring the following is at the top of
your HTML `.php` files:

```php
namespace Inkwell\HTML;
```

### Escaping

Escaping can be done explicitly with:

```php
<?= html::esc($value) ?>
```

If no additional filters are set, escaping is done automatically on `html::out()`.

### Outputting

Outputting values will pass the value through all filters currently set, by default, this is
only escaping:

```html
<?= html::out('This & that') ?>
```

### Filtering

You can set the filters for a particular block of code by doing the following:

```html
<?php html::filter(['raw', 'lower', ...], function() { ?>
	Any call to <?= html::out('html::out') ?> will pass the value through `raw` and `lower` first.
<?php }) ?>
```

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
<?= html::raw('<a href="/">Go Home</a>') ?>
```

This will output the anchor as shown instead of escaping to make the code visible.  That is, it
will actually make a link.

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

### Lower

You can lowercase a string using `html::lower()`:

```html
I prefer my values to be lowercase, such as <?= html::lower($value) ?>
```

### Money

You can use the `money()` to output monetary values.

```html
<?= html::money(2) ?>
```

Will output `$2.00` by default.  Unlike some simpler filters, money can be configured.  You can
configure it by performing the following prior to use.  If you're using this component as part of
the framework, this configuration is done for you:

```php
Inkwell\HTML\html::add([
	'money' => new Inkwell\HTML\money($currency, $decimal, $separator)
]);
```

## Adding Custom Filters

A filter is a fly-weight class which generally only has a single method `__invoke()` which is
added to the `html` facade with a particular alias (usually the class name).

You can see how `money` is added in the previous section.  Since that example is a bit more
complex since it can be configured with currency, decimal places, etc, let's look at the simpler
example for `lower`:

```php
<?php namespace Inkwell\HTML
{
	class lower
	{
		/**
		 * Make a value lowercase
		 *
		 * @access public
		 * @param mixed The value to make lowercase
		 * @return mixed The lowercased value, or original value if not a string
		 */
		public function __invoke($data)
		{
			return is_string($data)
				? html::out(strtolower($data), 'lower')
				: $data;
		}
	}
}
```

This filter could then be added to the `html` facade class such as:

```php
Inkwell\HTML\html::add(['lower' => new Inkwell\HTML\lower()]);
```

Take note that `add()` requires an array where the key is the filter as it will be called on the
`html` class itself and the value is the instantiated filter.  You could call this filter via
`html::low()` instead by registering it as follows:

```php
Inkwell\HTML\html::add(['low' => new Inkwell\HTML\lower()]);
```

If you make a call to an unregistered filter, the `html` facade class will try to instantiate
the filter as a class in the `Inkwell\HTML` namespace, so in most cases you don't actually need
to worry about registering.

Keep in mind that if you need to allow for additional filter configuration options, such as is
the case with money, you will likely always want to register this manually or ensure that the
`__construct()` method on the filter requires no parameters and provides sane defaults.
