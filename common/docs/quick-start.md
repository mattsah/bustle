Quick Start Guide to inKWell
============

Whether you simply prefer the inKWell components or aren't really sure where to begin, this guide
acts as a simple introduction to the official inKWell stack.  While it will not cover every detail
of configuration, integrating your own components, or even all aspects of the official components,
it will provide a great baseline for your first inKWell project or as a reference if you decide to
continue using the official components.

## Installation

In order to install inKWell you will need [composer](https://getcomposer.org/).  All references
to composer will simply use `composer` and not `composer.phar`, depending on how you've installed
it, you may need to call a different command.

### Create Your Project Folder

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

## Server Setup

The default docroot for an inKWell project is the `public` folder in the application root.  This
leaves all your classes, configuration, etc, back one directory.

You will want to configure your server to access this as the document root and ensure that the
`.htaccess` or ``.user.ini` are being read.  For Apache this means you'll have to allow overrides,
in the case of nginx, you will want to set up `try_files` to try the `index.php` entry point.

### Make Writable Writable

You will want to make writable owned by the same user or group as the web server.  On debian
servers, this user is often `www-data`, so for example you might run:

```bash
chown www-data <app_root>/writable
```

If you're running the local dev server then chances are this will already be writable if you
installed with the same user who is running the server.

## First Run

Once you've completed the steps above, if you have php available on your system via the CLI, then
you can test the installation by simply running `php bin/server` from the application root and
opening your browser to [localhost:8080](http://localhost:8080).

You should receive the following:

`The requested resource could not be found`

## Adding an Error Handler

Let's begin by making the above request a bit nicer to look at.  In order to do this we'll go over
the following components pretty quickly:

- Controllers
- Configuration
- Views / Templates

### Creating a Controller

The first step to making our `404` error a bit nicer is to create a controller.  In the root of
your project / application folder there should be a folder called `user`.  Go ahead and create a
sub-folder called `controllers`.

<div class="notice">
	<p>
		The "user" folder is named after the Unix "usr" folder which traditionally contained programs,
		scripts, and resources which were external to the mainline distribution, i.e. user built.
	</p>
</div>

Within the `controllers` folder we're going to create a new file called `ErrorHandler.php` and
paste the following code:

```php
<?php

	use Inkwell\Controller;

	class ErrorHandler extends Controller\BaseController
	{

	}
```

By extending the inKWell base controller, we'll have some functionality taken care of without
any additional work.

#### Adding Our Action

Since we're going to need a specific entry point, let's go ahead and add a simple method called
`notFound` to our newly created controller:

```php
public function notFound()
{
	return 'Nothing to see here folks.';
}
```

Once we have this added we can configure it.  But, before we can use the class we want to make
sure composer knows how to autoload it, so let's run the following:

```bash
composer dump-autoload
```

The entire `user` directory is classmapped by default which means when you add classes you'll
have to run the above command.  If you prefer PSR-0 or PSR-4, feel free to change this in the
`composer.json`. **No official inKWell package will ever rely on anything in the user directory.
It's your house!**

##### Handler Configuration

Now that we have our action added and composer seeing our class, let's go ahead and configure it.
Open the file located at `config\default\routing.php` and locate the section which appears as
follows:

```php
'handlers' => [

],
```

Now add your handler inside of the array:

```php
'handlers' => [
	HTTP\NOT_FOUND => 'ErrorHandler::notFound'
],
```

<div class="notice">
	<p>
		All inKWell configurations are PHP.  You may have noticed at the top of this file that the
		IW\HTTP namespace was being used.  This allows us to use the HTTP prefixed constants which
		come in handy for normalizing response codes and/or phrases.
	</p>
</div>

Reload your [localhost:8080](http://localhost:8080) page and you should now see your custom
error handler being used.  That's your first controller and error handler added.

Pretty simple huh?  But let's make it look better still!

### Using Views and Templating

Making pages prettier requires a bit more than simple strings.  In this next portion, we're going
to use the inKWell view component to load a custom template and give our `404` page some style.

Before we can begin using the view, we're gonna need to make it available.  Let's open our
`user/controllers/ErrorHandler.php` file again and add a `__construct()` method that looks like
the following:

```php
public function __construct(Inkwell\View $view)
{
	$this->view = $view;
}
```

<div class="notice">
	<p>
		The inKWell controller component adds a custom router resolver which uses the
		<a href="https://github.com/rdlowrey/Auryn">Auryn dependency injector</a> to construct our
		controllers.  This means that any dependencies you throw in the constructor method can be
		automatically resolved.
	</p>
</div>

Now that we have a view object made and ready to use, let's go ahead and create a template that
we'll be able to use.  Located in `user/templates` you should see an existing `master.html.php`
file.  This file is a basic placeholder which we can expand our templates with.  It's a simple
HTML5 template with no external requirements... so let's add some.

In the `<head>` element of that file, let's go ahead and add a simple CSS bootstrap:

```html
<link rel="stylesheet" href="//dotink.github.io/inKLing/inkling.css" />
<link rel="stylesheet" href="//dotink.github.io/inKLing/themes/default.css" />
```

And just in case we need some overrides, let's make sure we have our own to work with.  This
will `404` until we create it, but it may be good to have down the line:

```html
<link rel="stylesheet" href="/styles/main.css" />
```

To make sure we're on the right track, let's go back to our `ErrorHandler.php` and just see if
we can get that coming back no problem.  We'll replace our existing `notFound()` method with one
that returns the main template:

```php
public function notFound()
{
	return $this->view->load('master.html');
}
```

Again, we can reload our page at [localhost:8080](http://localhost:8080) and see if that took
effect.  Keep in mind that the master template had nothing in it except for a placeholder
`<title>` element, so you may have to look at the very top of your browser window, but if you're
using a browser inspector or just taking a look at the source code, it should be pretty clear
we've got some movements.

#### Template Expansion

One of the main problems with our last change was that we modified the master template directly.
While this is fine and useful for lots of *common* changes that we want to see site-wide, chances
are we don't want to have our master template only displaying our `404` content.

To resolve this, we're going to create a new template in a new directory, `user/templates/errors`.
Let's go ahead and make that directory and add a placeholder file called `not-found.html.php`.

<div class="notice">
	<p>
		The inKWell view object is designed to work with PHP templates, as such, it will always
		append the ".php" extension.  When you create new templates, it's important to remember
		to end them with ".php", but when you load them, you should leave it off.  The secondary
		".html" extension is optional, but it will tell inKWell to set up any available filters
		for the filetype.
	</p>
</div>

When we went to edit the `master.html.php` template, you may have recalled seeing the following
line:

```php
<?php $this->insert('content') ?>
```

This line is what tells the master content to place any child elements under the `content` key.
In order to get our not found template to use this, we can go ahead and create the file so it
looks like the following:

```html
<?php namespace Inkwell\HTML;

	$this->expand('content', 'master.html');
	$this->set('title', 'Sorry, the page you are looking for has disappeared.');

	?>

	<section role="main">
		<h1>Four! OH FOUR!</h1>
		<p>
			...she cried out in the middle of the night.
		</p>
	</section>
```

A few things you'll want to take note of:

```php
$this->expand('content', 'master.html');
```

This tells our `not-found.html.php` template that it's going to expand via the `master.html.php`
template by wrapping itself at the point of the `content` element being placed.

```php
$this->set('title', 'Sorry, the page you are looking for has disappeared.');
```

The above assigns a title, so even once expanded, the master template will share the data with
our not found template and allow us to customize higher level data from lower level templates.

```html
<section role="main">
	<h1>Four! OH FOUR!</h1>
	<p>
		...she cried out in the middle of the night.
	</p>
</section>
```

Just a simple bit of fun content.

Now that we've created our `not-found.html.php` template, let's go ahead and save it.  Return to
our `ErrorHandler.php` file and swap out our `notFound()` method's return statement once again:

```php
return $this->view->load('errors/not-found.html');
```

Once that's saved, we should be able to reload and see our custom `404` handler.

## Handling Requests

Handlers can be registered for any response greater than or equal to `400` which covers essentially
all error spaces for HTTP response, but these are handled on the outbound request.  Handling
inbound requests is not very different, although instead of mapping our controller actions to
particular status code / error handlers, we map them to a URL.

You can try this using our error handler by addressing it directly as if it were handling an
actual page request.  To do this, reopen the `config/default/routing.php` and look for the
following:

```php
'links' => [

],
```

Go ahead and add a direct URL:

```php
'links' => [
	'/404' => 'ErrorHandler::notFound'
],
```

Now hit the [localhost:8080/404](http://localhost:8080/404) to see the results.

<div class="notice">
	<p>
		Providing a "link" to the error handler actually results in a different response.  Namely,
		the status code on an actual missing page will be 404, and that which you link to and
		succeeds will be 200.
	</p>
</div>

### Dynamic URLs

In addition to static links like the above, you can do a lot more with dynamic URLs using the
router.  A dynamic URL will have a route where certain segmants are replaced with parameter
matching tokens.  URLs which match will then forward to the assigned controller.

For example let's add the following to the `links` section of the `config/default/routing.php`:

```php
'/birthday/[!:name]/[#:year]-[#:month]-[#:day]' => 'BirthdayController::age'
```

The above route indicates that we'll be looking for `/birthday/` followed by a name (which can
include anything but a slash), then another slash, then positive or negative integers.  While we
don't actually want negative integers, we're going to use this for now just for simple
demonstration.

We can handle this route by creating our controller with the appropriate action.  We'll stick this
in `user/controllers/BirthdayController.php`:

```php
<?php

	use Inkwell\Controller;

	class BirthdayController extends Controller\BaseController
	{
		public function __construct(Inkwell\View $view)
		{
			$this->view = $view;
		}

		public function age()
		{
			return $this->view->load('birthday/age.html');
		}
	}
```

Now let's create our corresponding view in `user/templates/birthday/age.html.php`:

```xml
<?php namespace Inkwell\HTML;

	$this->expand('content', 'master.html');
	$this->set('title', 'Congratulations on Being Alive!');

	?>

	<section role="main">
		<h1>Congratulations <?= $this('name') ?></h1>
		<p class="highlight">
			You've survived for <?= $this('age') ?> years!
		</p>
	</section>
```

Once all this is added and saved, let's make sure we refresh our autoloader to find the new
controller:

```bash
composer dump-autoload
```

Then let's go ahead and try to hit our new URL
[http://localhost:8080/birthday/Matt/1984-04-28](http://localhost:8080/birthday/Matt/1984-04-28).

You should see the heading "Congratulations" and "You have survived for years!"

This might seem a bit odd, since we wanted to output our name and age, but these values are not
set on the view, so they're defaulting to `NULL`.  In order to set them, we'll need to make some
modifications to our controller.

Let's go ahead and change our return line to the following:

```php
return $this->view->load('birthday/age.html', $this->request->params->get());
```

This passes our parameters directly to our view data.  While this is not something you should do
in every circumstance, it's a pretty quick to get a one-to-one mapping.

### Accessing URL Parameters

To access our parameters individually, we can pass their names to get.  Still working in our
controller, let's get our individual parameters before the return:

```php
$params = $this->request->params;
$name   = $params->get('name');
$year   = $params->get('year');
$month  = $params->get('month');
$day    = $params->get('day');
```

Now let's calculate our actual age:

```php
$dob = strtotime(sprintf('%s-%s-%s', $year, $month, $day));
$age = (time() - $dob) / (60 * 60 * 24 * 365);
```

Just to make sure we did our math correct, let's go ahead and dump that before moving on, add
this after we get the `$age` and reload the page:

```php
var_dump($age);
```

You should see:

```php
float 30.801489567478
```

<div class="notice">
	<p>
		The default behavior for the inKWell router is that controller actions are mutable.
		Although the controller will continue to execute, the printed output of a controller is
		preferred over the return value when the router is set to mutable, this is useful for
		debugging.
	</p>
</div>

That value looks about right (I'm getting old).  Let's take our final results and put it all
together.  Our final controller action should look like the following:

```php
public function age()
{
	$params = $this->request->params;
	$name   = $params->get('name');
	$year   = $params->get('year');
	$month  = $params->get('month');
	$day    = $params->get('day');

	$dob = strtotime(sprintf('%s-%s-%s', $year, $month, $day));
	$age = (time() - $dob) / (60 * 60 * 24 * 365);

	return $this->view->load('birthday/age.html', [
		'name' => $name,
		'age'  => floor($age)
	]);
}
```

Now we reload our page again and see the complete message.

### Custom URL Patterns

Although this is a really good example to demonstrate the basics, as we noted earlier.  There's
a few problems.  For one, we don't want to accept negative numbers.  Secondly, we'd probably
prefer if it were a single parameter for the date of birth rather than 3 that we need to string
together.

You can use any valid regular expression in place of a pattern token in a route, so long as the
following requirements are met:

- The RegEx only has a single match (use non-capturing groups if needed)
- It's surrounded in parenthesis

Let's go ahead and change our route to the following:

```php
'/birthday/[!:name]/[([0-9]{4}-[0-9]{2}-[0-9]{2}):dob]' => 'BirthdayController::age'
```

While this format is a bit more verbose, we're specifically trying to only match potential
birthdays.  This is still not perfect, cause we'll end up accepting months and days larger than
the number of months in a year or days in any month, but again, good for demonstration.

Once we've changed this route, we can now shorten our controller logic to look like this, getting
our date of birth more directly:

```php
public function age()
{
	$params = $this->request->params;
	$name   = $params->get('name');
	$dob    = strtotime($params->get('dob'));
	$age    = (time() - $dob) / (60 * 60 * 24 * 365);

	return $this->view->load('birthday/age.html', [
		'name' => $name,
		'age'  => floor($age)
	]);
}
```
