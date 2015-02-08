# Templating

Templating is the other side of the `View` object.  Here we will address how to take everything
we learned in [the view documentation](./01-views) and make use of it inside our templates,
including:

- Expanding templates (aimilar to inheritance)
- Inserting / injecting subcomponents
- Buffering for fun and profit.

If you haven't read the view documentation, we highly recommend doing so before moving on.

## Installation

```
composer require dotink/inkwell-view
```

## Basics

Templates are inevitably just included PHP files in the scope of the `View` object.  They should
be predominately HTML with a light smattering of PHP for control logic and not much else.  To get
an idea of what they look like right off the bat, open the `user/templates/master.html.php` which
is distributed with the view component:

```xml
<?php namespace Inkwell\HTML; ?>

<!doctype html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?= $this('title') ?: 'Welcome to inKWell' ?></title>
	</head>
	<body>
		<?php $this->insert('content') ?>
	</body>
</html>
```

<div class="notice">
	<p>
		Namespacing is not required, but if you're using a filter such an `Inkwell\HTML\html` it
		will obviously make the manual calls to the filter a lot shorter.
	</p>
</div>

This basic template represents a simple HTML5 document and only uses two dynamic elements.  First
it will print the `title` set in the view data, or if not found, the constant string
`'Welcome to inKWell'`.  Secondly, it attempts to insert the `content` subcomponent in between
the `<body>` tags.

### Expanding Templates

As should be rather obvious, it would be a bit redundant if you had to constantly load the master
template only to create a subcomponent for the actual view you wanted.  Instead, when you load
a view, you will usually be loading the primary content directly and "expanding" it based on
a container template.  To do this, you can use the `expand()` method inside the template to say
which template it expands into and which component it represents.  For example, the following
template would actually render it's content inside the `master.html.php` template where the call
to `$this->insert('content')` is located:

```xml
<?php namespace Inkwell\HTML;

$this->expand('content', 'master.html');

$this['title'] = 'My First inKWell Site';

?>
<section role="main">
	<h1>A New Day is Dawning</h1>
	<p>
		This site is built in inKWell 3.0, and it's a joy to maintain!
	</p>
</section>
```

In addition to filling in the `<body>` tags of the `master.html.php` template, you can see that
we also set the title there.  Whenever you expand a template, all aspects of the template are
shared, so setting data on one sets the same data on the other.  This allows changes to bubble up
the expansion chain.

You may recall from [the view documentation](./01-views) that subcomponents will, otherwise, at most, get
copies of data and components.

### Injecting Partials

Similar to expand, we can inject partials directly.  When a partial is injected, it is actually
execute in the exact same view object scope as the template which is injecting it.  It has no
container to speak of and is essentially the same as doing an `include` in native PHP with the
exception that it will still use the root directory and `.php` extension convention:

```xml
<?php namespace Inkwell\HTML;

$this->expand('content', 'master.html');
$this->inject('site/header.html');

?>
<section class="group" role="main">
	<div class="principal">
		<?php $this->insert('principal') ?>
	</div>
	<div class="prologue">
		<?php $this->insert('prologue') ?>
	</div>
</section>
<?php

$this->inject('site/footer.html');

?>
```

The above represents what we might call a "layout."  It provides a common header and footer as
well as basic page structure.  You can see we use the `inject()` method to include the header and
the footer as non-distinct subcomponents.

#### Inject vs. Insert Comparison

|  Inject                                |     Insert
|----------------------------------------|----------------------------------------------
| Requires template path                 | Requires subcomponent name
| Includes in same scope                 | Includes in separate view object scope
| Shares all subcomponents, data, etc    | Share all or none depending on subcomponent instantiation


### Buffering

The `buffer()` method is useful for capturing a section of your template for additional processing.
All it does is accept a closure which you can, in turn, add code between opening and closing:

```php
<?php $this['sample'] = $this->buffer(function() { ?>
	<a href="test">Testing</a>
<?php }) ?>

<?= $this('sample') ?>
```

The inner content is returned immediately when the method call finishes.  In the above example we
are using the buffer to capture some markup and then recalling it through our filters which will
cause it to become HTML encoded.

<div class="notice">
	<p>
		PHP code inside the buffer will still be executed when the closure is executed, so you
		can't use this to print HTML encoded PHP samples <em>unless there are no start tags</em>
	</p>
</div>

In addition to the above, you can often use buffering to create an array of possible HTML blocks
and then select from them randomly to display one, or randomize them (for carousels, ads, business
partners, whatever).
