# Routing

The inKWell router is a powerful yet lightweight router which provides all of the functionality of
traditional routers with a lot more flexibility.

## Installation

```bash
composer require dotink/inkwell-router
```

## Providers

| Via                         | Description
|-----------------------------|-----------------------------------------------------
| `$app['router']`            | The router [engine](https://github.com/dotink/inkwell-routing/blob/master/docs/classes/Inkwell/Routing/Engine.md).  Responsible for most major routing operations.
| `$app['router.collection']` | The [collection](https://github.com/dotink/inkwell-routing/blob/master/docs/classes/Inkwell/Routing/Collection.md) registered with the router engine.  You can add route/action links, handlers, and redirects to this.
| `$app['router.resolver']`   | The router [resolver](https://github.com/dotink/inkwell-routing/blob/master/docs/classes/Inkwell/Routing/ResolverInterface.md) that will be used for resolving router actions.

## Using Closures

The easiest way to get started with routing is merely to use closures.  You can do this by creating
a new action such as `include/default/routes/base.php`.  For example:

```php
return Affinity\Action::create(['core', 'routing'], function($app, $broker) {
	$app['router.collection']->link('/', '/hello/[!:name]', function(){
		return sprintf('Hello %s!', ucwords($this->request->params->get('name')));
	});
});
```

<div class="notice">
	<p>
		Note that the first parameter is a base url.  You may wish to group various related routes
		into distinct action files and allow for configuring the base URL in the config, for
		example `include/default/forums/routes.php`:
	</p>
</div>

```php
return Affinity\Action::create(['core', 'routing'], function($app, $broker) {
	$routes   = $app['router.collection'];
	$base_url = $app['engine']->fetch('forums', '@routing.base_url', '/forums');

	$routes->link($base_url, '/[!:topic]/', function(){
		//
		// Handle a Topic
		//
	});

	$routes->link($base_url, '/[!:topic]/[!:post]', function(){
		//
		// Handle a post in a topic
		//
	});

	$routes->link($base_url, '/users/[!:username]', function(){
		//
		// Show a user's profile
		//
	});

});
```

And then an example config `include/default/forums.php`:

```php
return Affinity\Config::create(['routing'], [
	'@routing' => [
		'base_url' => '/forums'
	]
]);
```

The above will allow you to easily change the base URL in the future or if you deploy the same code
to multiple instances.  You may, for example, wish to have a site which is solely forums, so you
change the `'base_url' => '/'`.

### Getting Context

As seen in the first example on this page, you can get the request object using a closure by simply
accessing `$this->request`, the same is true with the current response object which is available
as `$this->response`.

## Controller Resolver

By default the router will only work with object instances of type `Closure`, this is because in
order to provide meaningful context information to an action such as the request or response, it
needs to know an interface.  This is resolved with `Closures` because the router can bind the
closure to its own scope.  To resolve other types of actions, you can write a custom resolver
or [use the official controller component](02-controllers):

```php
use Auryn;
use Inkwell\Routing;

class Resolver implements Routing\ResolverInterface
{
	$broker = NULL;

	/**
	 * Your resolver will have dependencies injected.  If you need to resolve other
	 * dependencies, you can inject the broker itself.
	 */
	public function __construct(Auryn\Provider $broker)
	{
		$this->broker = $broker;
	}

	/**
	 * Resolves a router action to a final callback for the router to execute.
	 *
	 * @param mixed $action The action as passed directly to the router via `link()`, post compilation
	 * @param array $context Array containing routing context: 'router', 'response', 'request'
	 * @return Callable A callable action for the router to execute
	 */
	public function resolve($action, Array $context)
	{
		//
		// Resolve actions here
		//
	}
}
```

The resolver *must* implement `Inkwell\Routing\ResolverInterface` and the `resolve()` method must
return a callable which is executable via `$callable()`.  All other details are up to you.  Once
you've implemented a resolver, you can enable it by adding a new action or putting it into an
existing relevant action:

```php
$app['router.resolver'] = $broker->make('Example\Router\Resolver');
```

Alternatively, you may with to make the resolver more configurable.  The following line is taken
from the official controller component:

```php
$app['router.resolver'] = $broker->make('Inkwell\Routing\ResolverInterface');
```

By using the interface, instead, you can then add a provider to a config to make it more easily
configurable, e.g. `config/default/controller.php`:

```php
return Affinity\Config::create(['providers'], [
	'@providers' => [

		//
		// The provider mapping lists concrete class providers for given interfaces, the
		// interface is the key, while the class is the value.
		//

		'mapping' => [
			'Inkwell\Routing\ResolverInterface' => 'Inkwell\Controller\Resolver',
		]
	],
]);
```

## Parametization

Parametization is the process of converting particular pieces of a URL to variable parameters.
In a URL such as `/aritlces/1-this-is-a-title` it is often the case that the 1 represents a unique
ID for the article to be looked up.  You can match parameters in inKWell using the following
syntax:

```bash
/articles/[!:id]-[!:slug]
```

### Match Patterns

The parameter token is comprised of two pieces, firstly is a pattern and secondly is the parameter
name.  The `!` in the previous example actually represents a pattern matching anything other than
a forward slash.  Alternatively, you can specify a completely custom regular expression:

```bash
/calendar/[([0-9]{4}-[0-9]{2}-[0-9]{2}):date]/
```

If the URL does not match all patterns in the route, completely, the route will be skipped.  If it
does, the data in the position of the tokens will be parsed and added to the request object.

You can use the following characters in place of custom regular expressions (similar, to how `!`
	was used in the earlier example) in order match and parse some common data:

| Character | Regex                                        | Matches
|-----------|----------------------------------------------|-----------------------------------------
| $         | `([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)` | A valid PHP variable name
| +         | `([1-9]|[1-9][0-9]+)`                        | Any positive integer, not including 0
| %         | `([-]?[0-9]+\.[0-9]+)`                       | Positive or negative floats, useful for longitude and latitude
| #         | `([-]?(?:[0-9]+))`                           | Positive or negative integers, including 0
| !         | `([^/]+)`                                    | Anything not a slash
| \*        | `(.*)`                                       | Any character

## Target Compilation

Similar to parametization, it is also possible to signify variable data in target actions.  In
strings, these will be resolved before passing them to the resolver.  So, for example, if you're
resolver knows how to resolve the string `'MyController::action'` you can make the controller
action itself variable by using target compilation:

```php
$app['router.collection']->link('/', '/[$:class]/[$:action]', '[uc:class]Controller::[lc:action]');
```

The above line will attempt to resolve different controllers and actions depending on the URL.
Similar to parametization, tokens are comprised of a pattern indicator and a parameter name.  The
parameter name must match a parameter parsed from the route (not just any request data), and
parameters which are used for compilation will not be added to the request, so they won't conflict
with `GET` or `POST` parameters.

The pattern portion of a compiler token represents a transformation style.  In the example given
above the `uc` is short for UpperCamelCase style while the `lc` is short for lowerCamelCase style.

The following compilation patterns are supported:

| String   | Style
|----------|------------------------------------------------------
| uc       | UpperCamelCase
| lc       | lowerCamelCase
| us       | under_scored

## Configuration

Every example, thus far, has shown the addition of routes via an action.  This is because again, by
default, the router does not resolve anything other than `Closure` objects.  Although it is possible
to add closures to configs (since configs are also PHP), it's a bit less elegant.

Once a resolver is added, however, the resolver can instantiate objects based on strings.  It's then
much nicer to add routes to configuration instead of an action.  The `routes` aggregate ID can be
used in order to configure routers which will be added to the router:

```php
return Affinity\Config::create(['routes'], [
	'@routes' => [
		'base_url' => '/',
		'links' => [
			'/[$:method]/'                             => 'MainController::[lc:method]',
			'/[$:class]/[$:method]/'                   => '[uc:class]Controller::[lc:method]',
			'/[$:namespace]/[$:class]/'                => '[uc:namespace]\[uc:class]Controller::list',
			'/[$:namespace]/[$:class]/[+:id]-[!:slug]' => '[uc:namespace]\[uc:class]Controller::select'
		]
	]
]);
```

The above will register all links in the '/' base URL with the target actions.  Again, this type of
configuration will require a resolver, but it provides a much cleaner way of organizatin your
routes than traditional closures.

### General Settings

In addition to being able to configure routes in any configuration per a base URL, there are a few
options for controlling the router behavior itself which can be found in the `config/default/routing.php`
file.

#### Mutable

Set the `mutable` key to `TRUE` if you wish to allow output echoed from the action to overload the
returned response.  This is useful in development because you can `echo` or `var_dump` output
without having to return it.

This is also useful if you're working with existing controller or application code which
traditionally started outputting instead of returning the output.

#### Restless

The `restless` key controls whether or not the inKWell router will attempt to append or remove
trailing slashes from URLs.  When set to `TRUE` if a route does not match, it will try the
alternative and perform an automatic permanent redirect if the alternative matches.

Setting this to `FALSE` during development is suggested as it will ensure you receive 404 responses
for non-canonical links.  However, once you move to production you may want to set this to `TRUE`
for usability.
