# Views

The inKWell view component allows you to load and render templates in a modular fashion which
allows for templates to act as both partials or as main pages without any additional changes or
method calls.

## Installation

```
composer require dotink/inkwell-view
```

## Creating a View

While it's possible to instantiate view objects directly, normally you'll want to have them
dependency injected.  The view component adds an additional action and configuration which ensures
views are instantiated with a common root directory when depency injected or created via Auryn.

```php
return Affinity\Action::create(['core'], function($app, $broker) {
	$root_directory = $app['engine']->fetch('view', 'root_directory', 'user/templates');

	$broker->define('Inkwell\View', [
		':root_directory' => $app->getDirectory($root_directory)
	]);
});
```

If you're using [the inKWell controller component](../handling-requests/02-controllers) you can
add the view directly to your constructor:

```php
use Inkwell\View;

public function __construct(View $view)
{
	$this->view = $view;
}
```

Alternatively if you instantiate views directly, you'll want to pass them the root directory as
the first argument:

```php
$view = new View('/path/to/my/views');
```

## Asset Handling

Previous versions of inKWell attempted to integrate asset management into the view object.  While
the new view component doesn't do this, it does allow for you to assign a separate asset manager
via the second argument.

You can modify the view action to add this parameter:

```php
$broker->define('Inkwell\View', [
	':root_directory' => $app->getDirectory($root_directory),
	':assets'         => new AssetManager()
]);
```

Or pass it directly as a second argument:

```php
$view = new View('/path/to/my/views', $asset_manager);
```

## Setting Up the View

The view object works with simple templates which use embedded PHP for their template logic.  When
you load a view, the '.php' extension will be automatically appended to the template.  You can,
however, specify a view type by using double extensions.

```php
$view->load('mytemplate.html');
```

This would use the the `mytemplate.html.php` template in the root directory.  The type can be
used to apply additional filtering:

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

### View Data

Once you have your view set up, you can begin adding data to it using the `set()` method. This
method accepts either an array (as a single parameter) for bulk assignment or as individual calls.

#### Setting

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

Although lots of data is set in controllers, soemtimes it makes more sense for data to be set in
templates.  Data such as page title, meta descriptions, etc, are often times better kept inside the
template so they can be modified by front end developers and because they are more frequently
associated with the particular template than a given controller.

The view object can be accessed inside a template via `$this` and you can set values using the
`ArrayAccess` interface:

```php
$this['title'] = 'Welcome to inKWell';
```

#### Verifying Data Exists

You can use the `has()` method to check if a particular piece of data is set on a view.  Note that
this will check if the actual key is set, so if the value is `NULL`, it will still return `TRUE`.

<div class="notice">
	<p>
		Retrieving data from an inKWell view will automatically return `NULL` for a non-existent
		value, so the default behavior is that if a value is et to `NULL` it removes it from the
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

### Getting Data

The best way to retrieve view data is using the `__invoke` method which means you call the view as
a function.  As with one of our previous examples, we refer to the view as `$this` inside the
template:

```php
<?= $this('title') ?>
```

Using this method, it's also possible to get keyed data in arrays, object properties, or even call
getters on an object:

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

<div class="notice">
	<p>
		Using the `__invoke()` method, any data you access will be filtered by the applicable
		callback registered with the `filter()` method.  In our previous example, this would mean
		string data is returned HTML encoded.
	</p>
</div>

If you need to bypass filters completely, you can still access data outside or inside the
template using the `ArrayAccess` mechanism:

```php
<?= $this['title'] ?>
```

## Rendering

The easiest way to render a view from within a controller or closure is to simply return it.  This
assumes you're using the inKWell router and the http stack which provides the requisite gateway
for rendering the view:

```php
return $view;
```

If you need to render the view directly or pass it to another routing solution as a string, you
can use the `compose()` method:

```php
$html = $view->compose();
```

Composition will render all contained elements which are inserted in the view, and if the view
is contained in a parent element for expansion will render itself into the parent view.  The
gateway looks for the `compose()` method on returned object responses and executes this on the way
out, so there's no distinct  difference between in-controller/closure compilation other than
that outgoing middleware may be able to modify the view if not compiled.
