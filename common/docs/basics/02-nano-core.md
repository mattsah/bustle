# Nano Core

The core of inKWell is comprised of only three major components.

1. The Application Helper
2. The [Affinity](https://github.com/dotink/affinity) Boostrapper
3. The [Auryn](https://github.com/rdlowrey/Auryn) Dependency Injector

Everything else is a plugin or extension.

## What's in the Box?

Out of the box inKWell provides a single entry `init.php` script which concerns itself with
initializing the above components.  Additionally some directory structure is provided to work
with the defaults, although these are easily changed if you don't like it.

You can get the application as a return result from this file:

```php
$app = include '/path/to/app/root/init.php';
```

You can then run your application code as follows:

```php
$app->run(function($app, $broker) {
	//
	// Your Application Code Here
	//
});
```

## What else can I do?

Add a configuration (`config/default/datetime.php`):

```php
return Affinity\Config::create([
	'timezone' => 'US/Eastern',

	'formats' => [
			'TIMESTAMP\ARTICLE'  => 'F j, Y, g:i a',
			'TIMESTAMP\CALENDAR' => 'm.d.y'
	]
]);
```

Use your configuration (`include/default/datetime.php`):

```php
return Affinity\Action::create(function($app, $broker) {
	date_default_timezone_set($app['engine']->fetch('datetime', 'timezone'));

	foreach ($app['engine']->fetch('datetime', 'formats', []) as $constant => $format) {
		define($constant, $format);
	}
});
```

Now in your application code, you can go ahead and do the following:

```php
Posted On: <?= date(TIMESTAMP\ARTICLE, $article->getDatePosted()) ?>
```

Overload your default config based on an environment (`config/west/datetime.php`):

```php
return Affinity\Config::create([
	'timezone' => 'US/Pacific'
]);
```

Then set the environment via your apache config:

```
SetEnv IW_ENVIRONMENT west
```

Whether you're using environments to differentiate settings based on development vs. production,
east vs. west, kiosk vs. mobile, inKWell makes it simple to write extensible bootstrapping and
configuration which will allow you to rapidly change or modify even the most complex applications
down the line and in line with specific needs of a given deployment.  Using simple PHP arrays and
closures, there's nothing to limit you other than your imagination.

We'll cover more advanced topics such as aggregate and pluggable configurations as well application
providers and action dependency ordering in other areas.
