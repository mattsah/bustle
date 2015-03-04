# Nano Core

The inKWell core provides only the most basic components for creating an application.  The core
alone is suggested for people who have highly specific requirements or who want to integrate
third-party components.

The core provides:

- An application container
- Configuration and bootstrapping utilities
- Dependency injection

Using just the core, you can integrate alternative routers, controllers, ORMs, etc using the same
configuration and bootstrapping system as inKWell's official components.

<div class="notice">
	<p>
		If you're not looking to start this bare bones, you can jump ahead to the
		<a href="../handling-requests/01-routing">routing documentation</a> to get started
		handling requests.
	</p>
</div>

## What's in the Box?

1. The [Application Container](https://github.com/dotink/inkwell-core) which provides a simple
container and useful helper methods for the most basic application interfacing tasks.
2. The [Affinity Boostrapper](https://github.com/dotink/affinity) which gives you an easy and
pluggable configuration and bootstrapping system.
3. The [Auryn Dependency Injector](https://github.com/rdlowrey/Auryn) which enables you to keep
parts of your application loosely coupled and resolve dependencies in an automated way.

Everything else is a plugin or extension.

## Getting the Application Instance

The entire bootstrapping process is contained in the `init.php` file at the application root.

This script concerns itself with initializing the aforementioned components and returning an
application instance which can then be used to access service providers and configuration during
the bootstrap process and finally execute your application logic.

To get the application instance simply do:

```php
$app = include '/path/to/app/root/init.php';
```

If you've created and are working in a `public/index.php` file you may wish to use the relative
path:

```php
$app = include '../init.php';
```

## Running Your Application Code

Once you have the application instance, you can run your main application code as follows:

```php
$app->run(function($app, $broker) {

	//
	// Your Application Code Here
	//

});
```

If you're using an MVC style architecture, chances are this will instantiate and run your router.
However, you can just as easily use a more traditional approach and have your application code
resolve individual files for inclusion.

Your application code is completely up to you.

## Using the Application Container

The application instance is a simple container which implements `ArrayAccess` and allows you to
store object instances and other information during application bootstrapping or in your main
application code.

<div class="notice">
	<p>
		Using the application instance as a service locator is <em>highly discouraged</em> and
		you should not rely on contained providers outside of your bootstrapping code.
	</p>
</div>

To use the applicaiton container, simply assign things to it as if it were an array:

```
$app['router'] = new My\Router();
```

## Resolving Dependencies

The `$broker` variable provided to bootstrapper actions (as seen in a previous example) contains
an instance of the Auryn dependency injector which you can use to configure and resolve class
instantiation.

Although inKWell uses Auryn, it is a separate project and offers additional documentation and
support at [https://github.com/rdlowrey/Auryn](https://github.com/rdlowrey/Auryn).

The basics are as follows:

### Instantiating an Object

```php
$app['router'] = $broker->make('My\Router');
```

### Delegating Instantiation to a Callback

```php
$broker->delegate('My\Router', function($routes_directory) {
	return new My\Router($routes_directory);
}, $app->getDirectory('routes'));
```

### Define Instantiation Parameters

Does the same as above, but with an explicit parameter:

```php
$broker->define('My\Router', [':routes_directory' => $app->getDirectory('routes')]);
```

### Preparing Objects Which Implement an Interface

```php
$broker->prepare('My\Interesting\Interface', function($instance, $broker) {
	$instance->someInterfaceMethod('...');
});
```

## Bootstrapping

While it's possible to run the above examples inside the main application callback, this often
results in a huge entry file which is difficult to maintain long term and has little to no clear
separation of configuration values from application bootstrapping logic.

Enter [the Affinity boostrapper](https://github.com/dotink/affinity).


Affinity is responsible for handling the configuration, initial wiring of dependencies, class
configuration, etc.  It provides a very clear separation between configuration values from
configuration logic and allows for you to easily plug in new configurations and bootstrapping
actions from third parties.

### Directory Structure

Affinity uses two main directories in the application root to house its separate concerns.  The
default configuration root is `config` and the default action root is `include`.

Within each directory you will also find a `default` subdirectory which houses the default
bootstrap configurations and actions for your application.  Each subdirectory represents settings
for a specific environment.

<div class="notice">
	<p>
		All your common configuration and bootstrapping should go into the default directory, this
		directory will always be included and the more specific environment directories simply
		overload settings or actions.
	</p>
</div>

The directory structure inside each environment folder is up to you, although it is suggested you
use additional sub directories for namespacing purposes.

The relative path to a given config file or action is used by affinity as a means to identify the
configuration or action.  So, for example `config/default/core.php` is identified by the simple
string `'core'` while a file such as `include/default/routes/main.php` is identified by
`'routes/main'`.

### Configuration

If you wish to change the configuration root to a different folder, you'll need to set the
`IW_CONFIG_ROOT` environment variable to a different location.  The location can be absolute or
relative to the application root.  The following example shows how to change this to `settings`
inside your Apache config.

```apache
SetEnv IW_CONFIG_ROOT settings
```

#### Creating a Configuration

You can create a configuring by using the `Affinity\Config::create()` method and returning it
from any PHP file located in a configuration directory.  For example, let's imagine adding the
following to `config/default/test.php`:

```php
return Affinity\Config::create([

	'key' => 'value',

	'parent' => [
		'child' => 'value'
	]
]);
```

The `create()` method takes two parameters.

- An optional first parameter, an array of configuration "types" which map to aggregate IDs (more
  on this later).
- The configuration array itself.

Both arguments are simple arrays, and if you only pass one, it is assumed to be the configuration
alone.

#### Accessing Configuration Data

Once a config is created, you can access configuration data by using the `fetch()` method on the
affinity engine, which is registered as `engine` in the application container:

```php
$app['engine']->fetch('test', 'key', 'default');
```

The parameters for the `fetch()` method are the configuration id, the parameter within that
configuration, and the default value if it's not found, respectively.  You can access deeply nested
data using a javascript style object notation for the second parameter:

```php
$app['engine']->fetch('test', 'parent.child', 'default');
```

##### Aggregate IDs

In addition to identifying a specific configuration to fetch data from, it is also possible to
specify types of information which may be provided by multiple configurations using an aggregate
ID.  All aggregate IDs must begin with `@`:

```php
$app['engine']->fetch('@providers', 'mapping', array());
```

In order to provide information for aggregate ID fetches, you need to pass an optional first
parameter to the `Affinity\Config::create()`` method containing a list of aggregates which that
configuration provides.

The data is then keyed under the aggregate ID within the config itself:

```php
return Affinity\Config::create(['providers'], [
	'@providers' => [
		'mapping' => [
			'Dotink\Package\UsefulInterface' => 'My\Concrete\ProviderClass'
		]
	]
]);
```

<div class="notice">
	<p>
		The @providers aggregate ID is used by the inKWell core to set up aliasing of interfaces
		to concrete class names for Auryn.  This allows a user to easily swap out a concrete
		provider simply by changing the config (so long as it provides the same interface).
	</p>
</div>

When fetching information from an aggregate ID, the returned array consists of one entry for
every configuration file which provides that aggregate data keyed by the specific configuration
id.  In the case of the above mappings, this means we have to first loop over the individual
configuration data, and *then* over the mapping themselves.

You can fetch a list of specific IDs which provide aggregate data by fetching the aggregate ID
alone, without specifying a parameter. This is how it's done in the `core` action to set up
providers:

```php
foreach ($app['engine']->fetch('@providers') as $id) {
	$provider_mapping = $app['engine']->fetch($id, '@providers.mapping', []);
	$provider_params  = $app['engine']->fetch($id, '@providers.params',  []);

	foreach ($provider_mapping as $interface => $provider) {
		$broker->alias($interface, $provider);
	}

	foreach ($provider_params as $provider => $params) {
		$broker->define($provider, $params);
	}
}
```

### Actions

Actions are pieces of modularized and pluggable logic which use the configuration data in order to
prepare your application for running.  Some of their main functions include:

- Setting up dependency wiring
- Running static class methods for config or setting static class properties for config
- Registering providers in the application container

Unlike configs which are just arrays of information, actions represent callable logic.

Similar to configurations, you can change where actions are loaded from by adjusting the
`IW_ACTION_ROOT` environment variable.  By default, they will run from `include` and, again,
according to the environment.

#### Creating an Action

Add a file to the appropriate environment (usually default) under the `include` directory and
return `Affinity\Action::create()`:

```php
return Affinity\Action::create(function($app, $broker) {
	$app['router'] = $broker->make('My\Router');

	foreach ($app['engine']->fetch('@routes') as $id) {
		foreach ($app['engine']->fetch($id, '@routes', array()) as $route => $file) {
			$app['router']->add($route, $file);
		}
	}
});
```

The `create()` method for actions takes two arguments:

- An optional first argument which is an array specifying the IDs of actions which needs to be run
  prior to the one we're creating.
- A callback which will be provided the initial context passed to Affinity when it is started.  This
  can vary for other affinity setups, but within inKWell this context includes the `$app` container
  and the `$broker` which is an instance of the Auryn dependency manager.

The above example shows what an action bootstrapping our a router might look like.  The
hypothetical router just maps routes to a specific file for inclusion, but demonstrates how we
use the configuraiton and actions in combination.  An appropriate configuration for the above
example might look like the following:

```php
return Affinity\Config::create(['routes'], [
	'@routes' => [
		'/'            => 'home.php',
		'/users/'      => 'users/list.php',
		'/users/{id}'  => 'users/select.php',
	]
]);
```

If, for example, we need to ensure that the router setup action always runs after a `core` action,
we can do the following to create the action, note the array as the first parameter:

```php
return Affinity\Action::create(['core'], function($app, $broker) {
	...
```

Bootstrapper dependencies should be considered dependencies of the package as a whole, since
Affinity will error if a startup dependency is required to run first, but is not available.

## Pulling it Together

All bootstrapping configurations and actions are loaded and executed prior to your main application
code running.  Finalizing the above example, assuming our router knew all it needed to know from
our bootstrapping, our application code might not do much other than:

```php
$app->run(function($app, $broker) {
	$app['router']->handle($_SERVER[REQUEST_URI]);
});
```

If you don't want to create your own router and don't have another preferred router that you'll
be configuring to work in your inKWell application, you might want to check out the
[routing documentation](../handling-requests/01-routing) to see what ours can do.
