# Routing

The inKWell router is a powerful yet lightweight router which provides all of the functionality of
traditional routers with a lot more flexibility.

## Installation

```bash
composer require dotink/inkwell-router
```

## Basic Usage

```php
use Inkwell\Routing;
use Inkwell\HTTP;

$collection = new Routing\Collection();
$response   = new HTTP\Resource\Response();

$collection->base('/', function($group) {

	//
	// Link a matching URL to a particular action
	//
	$group->link('/hello/[!:name]', function() {
		return 'Hello ' . ucwords($this->request->params->get('name'));
	});

	//
	// Handle error responss from actions
	//
	$group->handle(HTTP\NOT_FOUND, function() {
		return 'Goobye!';
	});

	//
	// Redirect a route to a new URL
	//
	$group->redirect('/', '/hello/world', HTTP\REDIRECT_PERMANENT);

});

$router = new Routing\Engine($collection, $response);
```


## Using Closures

The easiest way to get started with routing is merely to use closures as seen above in the
basic usage.  You can link a route to a particular action using the `link()` method.

```php
$collection->link('/', '/about', function(){
	return 'I am not telling you anything!';
});
```
<div class="notice">
	<p>
		The first parameter is a base url.  You can group multiple router actions on the
		collection under a common base url more easily using the `base()` method.
	</p>
</div>

### Getting Context

As seen in the first example on this page, you can get the request object using a closure by simply
accessing `$this->request`, the same is true with the current response object which is available
as `$this->response`.  Since closures are bound to the router itself, to get the router you need
only to access `$this`.

## Grouping Router Actions

You can use a `BaseGroup` to proxy collection methods under the same base URL.  In order to get
a basegroup for a given base URL you can use the `base()` method on the collection:

```php
$collection->base('/forums', function($group) {

	//
	// Handle a Topic
	//
	$group->link('/[!:topic]/', function(){
		...
	});


	//
	// Handle a post in a topic
	//
	$group->link('/[!:topic]/[!:post]', function(){
		...
	});


	//
	// Show a user's profile
	//
	$group->link('/users/[!:username]', function(){
		...
	});
});
```

The above will allow you to easily change the base URL in the future or if you deploy the same code
to multiple instances.  You may, for example, wish to have a site which is solely forums, so you
change the base URL to `'/'`.

## Running the Router

In order to run the router you'll need to pass it a request object.  While we encourage you to
[learn more about requests objects](./03-requests), the easiest way to do this is to create
an empty request an populate it with the HTTP gateway server.

```php
use Inkwell\HTTP;

$request = new HTTP\Resource\Request();
$gateway = new HTTP\Gateway\Server();

$gateway->populate($request);

$response = $router->run($request);
```

Running the router will return a controller's return value wrapped in a response object.  You can
transport the response using the same gateway:

```php
$gateway->transport($response);
```

## Beyond Basics

Until now, we've only covered the most straightforward and basic usage of the routing components
and some related HTTP components.   While this can handle pretty basic sites, often times you will
need much more advanced routes and target actions, not just simple parameter matching and closures.

Below you will find information pertaining to how to do more advanced routing using the components
we've already seen.

### Resolving Actions

By default the router will only work with object instances of type `Closure`, this is because in
order to provide meaningful context information to an action such as the request or response, it
needs to know an interface.  This is resolved with `Closures` because the router can bind the
closure to its own scope which means `$this` in the context of a closure *is* your router.

To resolve other types of actions, you can write a custom resolver or use the one provided by
[the official controller component](02-controllers).

A basic resolver class looks like the following:

```php
use Inkwell\Routing;

class Resolver implements Routing\ResolverInterface
{
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
return a callable which is executable via `$callable()` or `FALSE` if it cannot resolve the action.
All other details are up to you.  Once you've implemented a resolver, you can enable it by adding
it as a second parameter at runtime:

```php
$router->run($request, $resolver);
```

The `resolve()` method can do essentially anything you want.  It will be passed the original target
action as provided to `link()` to the first parameter.  Additionally, since you may need to work
with the router, request, or response to help determine the returned callable or to pass them
along to it, these are provided in a second parameter called `$context` which has, minimally, the
following structure:

```php
$context = [
	'router'   => $router,
	'request'  => $request,
	'response' => $response
];
```

As a simple example, if you merely wanted to be able to router directly to PHP functions, you could
implement the following:

```php
public function resolve($action, Array $context)
{
	if (function_exists($action)) {
		return $action;
	}

	return FALSE;
}
```

This would then allow for the following:

```php
$collection->link('/', '/info', 'phpinfo');
```

In order to continue handling `Closure` objects as the router currently does, you can do the
following:

```php
if ($action instanceof Closure) {
	return $action->bindTo($context['router']);
}
```

### Parametization

Parametization is the process of converting particular pieces of a URL to variable parameters.
In a URL such as `/aritlces/1-this-is-a-title` it is often the case that the `1` represents a unique
ID for the article to be looked up.  You can match most parameters in inKWell using the following
syntax in the route:

```bash
/articles/[!:id]-[!:slug]
```

#### Match Patterns

The parameter token is comprised of two pieces, firstly is a pattern and secondly is the parameter
name.  The `!` in the previous example actually represents a pattern matching anything other than
a forward slash.  Alternatively, you can specify a completely custom regular expression:

```bash
/calendar/[([0-9]{4}-[0-9]{2}-[0-9]{2}):date]/
```

<div class="notice">
	<p>
		If you use custom regular expressions, it is important to note that they should only ever
		represent a single match.  As such, any internal groups should use the non-matching
		form of `(?:)`.
	</p>
</div>

If the URL does not match all patterns in the route, completely, the route will be skipped.  If it
does, the data in the position of the tokens will be parsed and added to the request object.

You can use the following characters in place of custom regular expressions (similar to how `!`
was used in the earlier example) in order match and parse some common data:

| Character | Regex                                         | Matches
|-----------|-----------------------------------------------|-----------------------------------------
| $         | `([a-zA-Z_\x7f-\xff][a-zA-Z0-9_-\x7f-\xff]*)` | A valid PHP variable name with dash separator
| +         | `([1-9]|[1-9][0-9]+)`                         | Any positive integer, not including 0
| %         | `([-]?[0-9]+\.[0-9]+)`                        | Positive or negative floats, useful for longitude and latitude
| #         | `([-]?(?:[0-9]+))`                            | Positive or negative integers, including 0
| !         | `([^/]+)`                                     | Anything not a slash
| \*        | `(.*)`                                        | Any character

### Target Compilation

Similar to parametization, it is also possible to signify variable data in target actions.  In
strings, these will be resolved before passing them to the resolver.  So, for example, if you're
resolver knows how to resolve the string `'MyController::action'` you can make the controller
action itself variable by using target compilation:

```php
$collection->link('/', '/[$:class]/[$:action]', '[uc:class]Controller::[lc:action]');
```

Using the above, a request to `/my/action` would literally compile an action of
`'MyController::action'` before providing it to the resolver.

As with parametization tokens, compilation tokens are comprised of a pattern indicator and a
parameter name.  The parameter name must match a parameter parsed from the route (not just any
request data), and parameters which are used for compilation will not be added to the request,
so they won't conflict with `GET` or `POST` parameters.

The pattern portion of a compiler token represents a transformation style.  In the example given
above the `uc` is short for UpperCamelCase style while the `lc` is short for lowerCamelCase style.

The following compilation patterns are supported:

| String   | Style
|----------|------------------------------------------------------
| uc       | UpperCamelCase
| lc       | lowerCamelCase
| us       | under_scored

## Configuring Behaviors

There are a few options for changing the behavior of the router and how it will respond in various
circumstances.

### Mutable

Putting the router in mutable mode means that any output generated by an action will be sent as
the response *instead of* the returned value from the controller.  This allows you to debug more
easily.

You can set this via:

```php
$router->setMutable(TRUE);
```

This is also useful if you're working with existing controller or application code which
traditionally started outputting instead of returning the output.

### Restless

Restless mode allows for non-canonical URL redirects.  When set to `TRUE` if a route does not
match, it will try the alternative (with or without a trailing slash) as well.  If the alternative
does match, an automatic permanent redirect is performed.

```php
$router->setRestless(TRUE);
```

Setting this to `FALSE` during development is suggested as it will ensure you receive 404 responses
for non-canonical links.  However, once you move to production you may want to set this to `TRUE`
for usability purposes.