# Views

The inKWell view component allows you to load and render templates in a modular fashion which
allows for templates to act as both partials or as main pages without any additional logic.

## Installation

```
composer require dotink/inkwell-view
```

## Basic Usage

Views are instantiated with a root directory and then load templates relative to that directory:

```php
use Inkwell\View;

$view = new View('/path/to/templates');

$view->load('home.html');

$view->set([
	'title' => 'Welcome to My Amazing Website',
]);

$view->append([
	'content' => 'blog/latest.php',
	'sidebar' => 'events/upcoming.php'
]);

echo $view->compose();
```

## Setting Up View Objects

Normally you will want your views to be created by dependency injection so that the root directory
can be added to the constructor automatically as well as a shared instance of the asset manager
and common filters.

Below you can see how these additional aspects can be set up on new or existing views.

## Asset Handling

Previous versions of inKWell attempted to integrate asset management into the view object.  While
the new view component doesn't do this, it does allow for you to assign a separate asset manager
via the second argument:

```php
$view = new View('/path/to/templates', $asset_manager);
```

### Filtering

You can add all filters for your template formats as an array at instantiation time as a third
argument.  The format is `['format' => $callable]`:

```php
$view = new View('/path/to/templates', $asset_manager, [
	'html' => new Inkwell\HTML\html()
]);
```

Optionally, you can add one at a time.  In this example we use a simple closure instead of a filter
class:

```php
$view->filter('html', function($data) {
	return is_string($data)
		? htmlentities($data)
		: $data;
});
```

Filtering will allow you to automatically escape data retrieved from the view.  If you need more
advanced filtering or transformation techniques, we suggest you check out the
[inKWell html component](../supplemental/01-html-helper) for HTML templates.

## Working With Templates

The view object works with simple templates which use embedded PHP for their template logic.
Templates themselves run in the scope of the view so `$this` will always refer to the view and has
access to all private/protected methods and properties.

### Loading

When you load a view, the `.php` extension will be automatically appended to the template path.
You can, however, use double extensions to automatically specify the view format.  So, for example
if you are working with HTML views, you might load a 'mytemplate.html':

```php
$view->load('mytemplate.html');
```

On the filesystem level, however, this will attempt to load `mytemplate.html.php` from the root
directory.  It's good practice to always specify a format.

If you would like to create an independent view object with it's own distinct components and data
but sharing the same assets or filters, you can use the `create()` method:

```php
$view->create('mytemplate.html');
```

### View Data

Once you have your view loaded, you can begin adding data to it using the `set()` method. This
method accepts either an array (as a single parameter) for bulk assignment or as individual calls.

#### Setting Data

Setting will destroy any existing data referred to by the same key(s).

```php
$view->set('user', $user);
```

Or, if you have more than one piece of data to set:

```php
$view->set([
	'user'   => $user,
	'groups' => $groups
]);
```

Although lots of data is set in other contexts, sometimes it makes more sense for data to be set in
templates.  Data such as page title, meta descriptions, etc, are often times better kept inside the
template so they can be modified by front end developers and because they are more frequently
associated with the particular template than a given controller or service provider.

Since the template runs in the scope of the view, the view object can be accessed inside a template
via `$this`.  Although it is not required, we suggest setting view data inside the templates using
the `ArrayAccess` interface as this is often times easier for front-end developers to understand:

```php
$this['title'] = 'Welcome to inKWell';
```

#### Verifying Data Exists

You can use the `has()` method to check if a particular piece of data is set on a view.  Note that
this will check if the actual key is set, so if the value is `NULL`, it will still return `TRUE`.

<div class="notice">
	<p>
		Retrieving data from an inKWell view will automatically return `NULL` for a non-existent
		value, so the default behavior is that if a value is set to `NULL` it removes it from the
		internal data array.
	</p>
</div>

If you need to verify whether a key actually exists for some reason, then you should always use
`has()` and not `isset()` on the array access interface.

```php
$view->set('false', FALSE);

if (!$view->has('false')) {
	//
	// The `has()` method  will return `TRUE`, because 'false' is set even though the value
	// resolves to `FALSE`
	//
}
```

#### Getting Data

You can retrieve raw data from a view using the `get()` method.  This is generally useful outside
of the template context, because you will want to get back exactly what you put in:

```php
$title = $view->get('title');
```
Inside the template, however, the best way to retrieve view data is using the `__invoke` method.

<div class="notice">
	<p>
		Using the `__invoke()` method, any data you access will be filtered by the applicable
		callback registered with the `filter()` method.  In our previous example, this would mean
		string data is returned HTML encoded.
	</p>
</div>


```php
<?= $this('title') ?>
```

Using this method, it's also possible to get keyed data in arrays, object properties, or even call
getters on an object by using javascript style object notation:

```php
<?= $this('user.firstName') ?>
```

The rules for this are as follows:

1. If the data is an array or object implementing array access the data is retrieved via array
access.
2. If the data is an object and a matching property is set, it will attempt to get the property
directly.
3. If the data is an object and no matching property is set, it will attempt to call `getData()`
where `Data` is the name.

This works recursively, so you can access deeply nested properties:

```php
<?= $this('user.group.name') ?>
```

If you need to bypass filters completely inside a template but still want to use a terse format,
you can still access data via the `ArrayAccess` mechanism:

```php
<?= $this['title'] ?>
```

### Components / Subviews

A single view will often times be representative of a number of templates, sometimes called
partials.  Partials can be assigned or appended in or outside the template context, and can be
inserted or injected from within.

To understand this more clearly it is important to understand that each view object has a container
element which is either `NULL` or a parent view.

#### Assigning a Component

```php
$view->assign('header', 'common/header.html');
```

<div class="notice">
	<p>
		When a component is provided as a template path, a view object is created with with the
		original root, assets, and filters of the container (in this case `$view`) and is given
		a copy of the components and data.
	</p>
</div>

You can assign multiple components at once by passing an array:

```php
$view->assign([
	'header' => 'common/header.html',
	'footer' => 'common/footer.html'
]);
```

#### Appending a Component

Assigning components will wipe out existing values, however, sometime you want to append to a
component instead.  For example, if you have a sidebar in which you want to be able to
progressively add components to, you can use `append()`:

```php
$view->append('sidebar', $notice);
$view->append('sidebar', $advertisement);
```

The `$notice` and `$advertisement` variables represent separately instantiated view objects in
the above example.  That is, to say, you can append one view object to another directly.

<div class="notice">
	<p>
		When a component is provided as a distinct view object it retains all its original
		settings, components, data.
	</p>
</div>


## Rendering

Views can be rendered directly by calling the `compose()` method.

```php
$html = $view->compose();
```

The data is returned, so you can also echo this directly:

```php
echo $view->compose();
```

Composition will render all contained elements which are inserted in the view, and if the view
is contained in a parent element for expansion will render itself into the parent view.
