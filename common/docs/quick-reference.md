Quick Reference to inKWell
============

This reference is designed for people who use inKWell but forget things about it, or for people
who have worked with a number of other frameworks and think they have a good enough grasp
of general principles to simply look at code examples and figure things out.

If that's not you, you might want to try [the quick start GUIDE](./quick-start).

## Installation & Configuration

Installing is how you get from doing nothing to doing something.  You will need to configure
your server with the `public` directory as the document root.

- The `public/index.php` is the entry point for web requests
- Apache users will see a usable `.htaccess` file included in `public`
- FPM / CGI users will find a `.user.ini` in `public`

### Create You Project Folder

```bash
mkdir <target>
```

### Install the Nano Core

```bash
composer create-project -s dev inkwell/framework <target>
```

### Install the Official Components

```bash
composer require dotink/inkwell-components
```

### PHP Built-In Dev Server

You can execute the built-in dev server using `php bin/server`.

#### Configuration

If you need to configure the server differently, check out `config/default/server.php`:

```php
<?php
	return Affinity\Config::create([
		'host' => 'localhost',
		'port' => '8080'
	]);
```

## Routing

Routing is how you get from a URL to executing specific PHP code either in a controller or
service provider.

- Routing can be configured across multiple modular configuration files and environments
- Configuration can be encapsulated to a base URL
- In addition to URL -> Actions, configuration includes error handlers and redirects

### Add a Config

File: `config/default/<sub path to my config>.php`

```php
<?php

	use IW\HTTP;

	return Affinity\Config::create(['routes'], [
		'@routes' => [
			'base_url' => '/',

			'links' => [

				// Your Routes Here

			],

			'handlers' => [

				// Your Handlers Here

			],

			'redirects' => [

				// Your Redirects Here

			]
		]
	]);
```

### Add Links / Routes

Links can be added by creating `'route' => $action'` entries in the `'links'` array of any
configuration which uses the `routes` aggregate ID.

#### Static

```php
'links' => [
	'/about/contact' => 'ContactController::form',
	...
]
```

#### Dynamic

Dynamic routes will contain one or more parser token which follows the format of
`[<pattern>:<param>]`.  The pattern can either be a built-in shorthand, or a custom RegEx.

##### Dynamic Route

```php
'links' => [
	'/users/[+:id]' => 'UsersController::view'
]
```

| Symbol | Function
|--------|---------------------
| +      | Matches positive integers 1+


##### Dynamic Target

You can use compiler patterns and params in the target name to make the target dynamic:

```php
'links' => [
	'/account/[$:method]' => 'AccountController::[lc:method]'
]
```

| Symbol | Function
|--------|---------------------
| $      | Matches a valid PHP variable name, but can also include dashes
| lc     | Compiles a string as lowerCamelCase


##### Custom Pattern

Custom patterns can be any valid RegEx.  They are surrounded by parentheses and should not contain
additional capturing groups:

```php
'links' => [
	'/[$:table]/[+:id]/[(edit|delete):method]' => 'Admin\[uc:table]Controller::[lc:method]'
]
```

### Add Handlers

Handlers are served when responses come back with a status code over `400` (or `9000` as implied).
You can have different handlers for different base URL groups, which will cascade, i.e. they will
use the closest matching, and then move on to a more broadly defined base group.

```php
'handlers' => [
	HTTP\NOT_FOUND      => 'ErrorController::notFound',
	HTTP\NOT_AUTHORIZED => 'AccountController::login'
]
```

### Add Redirects

Redirects work similar to links, in that you can have static or dynamic routes and targets.
They use the `redirects` array, however differ slightly in that:

- They are keyed by the redirect type first
- Don't generally use custom compilation patterns

```php
'redirects' => [
	HTTP\REDIRECT_PERMANENT => [
		'/articles/[+:id]' => '/news/articles/[id]',
		'/events'          => '/calendar'
	],

	HTTP\REDIRECT_TEMPORARY => [
		'/[(^(?:maintenance)):anything]' => '/maintenance'
	]
]
```

## Controllers

Controllers are collections of actions which handle request negotiation, input aggregation,
and service fulfillment.

### Create a Controller Classes

You don't have to extend the `BaseController` class, however, doing so will allow the default
resolver to establish the routing context on the controller.

File: `user/controllers/MainController.php`

```php
<?php

	use IW\HTTP;
	use Inkwell\View;
	use Inkwell\Controller;

	class MainController extends Controller\BaseController
	{
		public function __construct(View $view)
		{
			$this->view = $view;
		}

		public function home()
		{
			return $this->view->load('home.html');
		}
	}
```

#### Dump Autoloading

To detect the new controller for autoloading:

```bash
composer dump-autoload
```

### Dependency Injection

Dependencies are automatically injected into the constructor:

```php
use cebe\markdown\Markdown;

...


public function __construct(Inkwell\View $view, Markdown $markdown)
{
	$this->view     = $view;
	$this->markdown = $markdown;
}
```

### Routing Context

Routing context provides the router, request, and response relevant to that controller, you can
access all of these on the controller directly.

#### Checking the Entry Action

```php
public function action()
{
	if ($this->router->isEntryAction([$this, __FUNCTION__])) {
		//
		// We're in a sub request
		//
	}
}
```

#### Abandon the Current Action

This will cause the router to continue trying different routes.

```php
$this->router->defer();
```

#### Abandon the Current Context

This will cause the router to give up and jump to error handling.

```php
$this->router->demit();
```

### Working with Requests

Although provided by the routing context, the accessing and using the request is probably the
most important thing a controller does, hence, this section.

#### Getting Parameters

Parameters are stored as a property on the request.  Although it is not strictly required, inKWell
will, by default, populate these as a collection.  The `get()` method will allow you to get
parameters from the collection.

##### Get All Parameters as An Array

```php
$this->request->params->get()
```

##### Get Some Parameters as An Array

```php
$this->request->params->get(['id', 'slug']);
```

##### Get a Single Parameter

```php
$this->request->params->get('id');
```

##### Get a Parameter with a Default

```php
$this->request->params->get('page', 1);
```

##### Getting a Nested Parameter

```php
$this->request->params->get('person.name', 'Jesse Doe');
```

### Returning

You can return objects which implement a `compose()` method to render to strings.  Note, that this
is not an interface, but a soft requirement.  If you return an object from a controller that does
not implement such a method, you will receive an exception.

```php
return $this->view->load('example.html', $data);
```

#### Returning Strings

```php
return 'This is a string';
```

#### Returning Custom Responses

```php
return $this->response
	->setStatusCode('201')
	->setHeader('Location', $resource_url);
```

## Views & Templates

The view object stores data and subcomponents for use in a template.

### Loading a Template

The default template root is `user/templates`.  Templates should be loaded as `<path>.<format>`,
however, the template files themselves should have the `.php` extention in addition to their
format, for example: `user/templates/home.html.php`.

```php
$this->view->load('home.html', $initial_data);
```

### Setting Data

There are multiple ways to set data on a view, however, when working outside the template it
is recommended that the `set()` method is used.  This will throw an exception if you attempt to
override data within a view, thereby ensuring integrity from other sources.

```php
$this->view->set([
	'record' => $record,
	'user'   => $this->auth->entity
]);
```

### Getting Data

If you need to get previously set data out of a template it from outside a view, it is suggested
you use the `get()` method.  This will throw an exception if you attempt to get data that does not
exist, thereby ensuring integrity of the data you're working with.

```php
$record = $this->view->get('record');
```

### Checking Data

If you just need to see if data exists, use `has()`:

```php
if ($this->view->has('record')) {
	// Do something that requires record
}
```

### Using Subcomponents

Subcomponents are additional templates or views which can be inserted into a template by name
using the `insert()` method.  This is useful for normalizing templates and allowing multiple
subcomponents to be inserted with a single common alias.

```php
$this->view->assign([
	'sidebar' => 'common/join_callout.html'
]);
```

You can also append to an existing element:

```php
$this->view->append([
	'sidebar' => 'common/advertising.html'
]);
```

### Rendering

```php
$content = $this->view->compose();
```

### Working in Templates

A template is run in the scope of the view object.  The view object can be referred to as `$this`
inside the template.

#### Setting Data

Using the `ArrayAccess` method it is possible to overload data from a component template.

```php
$this['title'] = 'This will overload any title for a parent / master template';
```

### Getting Data

Using the `ArrayAccess` method, you can get view data which will return `NULL` if that data is
not available, thereby allowing for easy defaults:

```php
$record = $this['record'] ?: new Record();
```

### Filtering

Data which is accessed via the `__invoke` method will be passed through any registered filters for
the view format.  In the case of HTML, this includes escaping by default:

```php
$title = $this('title') ?: 'This is the default title';
```

Accessing nested data:

```php
$title = $this('article.title');
```

The above would attempt to get the title from the article as follows:

- If article is an array, the key `['title']`
- If the article is an object the property `title`
- If the article is an object and the method `getTitle()` is callable

## HTML Filter and Helper

The HTML filter should be applied inside any view with a format of `html`.  Formats are set
based on the extension of the template such that `example.html.php` is an HTML template.  You can
use the helper more generally just by accessing the class, but only HTML templates will have
data accessed via `$this()` automatically escaped.


```php
<h1><?= $this('title') ?: 'The default title' ?></h1>
```

### Manually Escaping

While the above example would automatically escape data on an HTML template, it's possible
to manually escape:

```php
<?= html::esc($some_value) ?>
```

### Looping

```html
<?php html::per($this('items'), function($i, $item) { ?>
	<!--
		$item and $i will be escaped if they're strings.
	-->
<?php }) ?>
```

If `$item` in the above example is an object which you will want to access additional properties
on without calling `html::out()` you can envelop it in the view to incorporate its parameters:

```html
<?php html::per($this('articles'), $this(function($i, $article) { ?>
	<h2><?= $this('article.title') ?></h2>
	<p class="summary">
		<?= $this('article.summary') ?>
	</p>
<?php })) ?>
```

### Multiple Filters

The `html::out()` method will apply all active filters.  If no filters are set, this defaults to
the escape filter.

```php
<?php html::filter(['raw', 'lower'], function() { ?>
		<?= html::out('<A HREF="/">Go Home!</a>') ?>
<?php }) ?>
```

The above would actually produce the output:

```html
<a href="/">go home!</a>
```

This would be unescaped, and therefore produce a link on your site rather than presenting the user
with the text as shown.
