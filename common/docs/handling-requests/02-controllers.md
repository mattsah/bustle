# Controllers

Because most people won't need custom resolution or highly specialized controllers, inKWell
provides a stock controller component which adds a base controller class as well as a standard
resolver for the router.

## Installation

```bash
composer require dotink/inkwell-controller
```

## Basics

The controller component provides both a base controller class as well as a number of traits and
interfaces which allow you to plug in various additional pieces of supporting functionality.

### Context Containment

The base controller acts as a publicly accessible container.  All container properties are stored
in an internal `context` property, so you don't need to worry about it conflicting with private or
protected properties.

You can get or set from the context using standard property getting/setting due to `__get()` and
`__set()` implementation.

```php
$this->view = $view;
```

Alternatively, you can access the context using `ArrayAccess`:

```php
$this['view'] = $view;
```

### A Basic Controller Class

Below is a basic controller class with a single entry method for handling a home page:

```php
use Inkwell\Controller;

class MainController extends Controller\BaseController
{
	/**
	 * Handles the homepage
	 */
	public function home()
	{
		//
		// Return your homepage content
		//
	}
}
```

The `BaseController` class provides some commonly used methods for request / response mediation
which will be covered more below.

### Instantiation / Action Resolver

Although it's possible to instantiate controllers directly, more often than not their actions will
be called by a router or similar entry point.  The resolver is a super lightweight factory which
can be provided directly to [the inKWell router](./01-routing) or wrapped to work with other routers
that employ similar resolution facilities.

The examples which follow show the capabilities of the resolver, however, you would not generally
resolve controller actions in this manner.

Optionally, the resolver can do resolution time constructor dependency injection if given
an instance of [the Auryn dependency injector](https://github.com/rdlowrey/Auryn):

```php
use Inkwell\Controller;
use Auryn;

$broker   = new Auryn\Provider();
$resolver = new Controller\Resolver($broker);
$action   = $resolver->resolve('MainController::home');
```

If you don't need dependency injection, you can leave that out.  In either case, you can
additionally provide an initial context for the controller, which will basically consume the
provided properties of an array into its container values:

```php
use Inkwell\Controller;

$resolver = new Controller\Resolver();
$action   = $resolver->resolve('MainController::home', [
	'foo' => $bar
]);
```

The return result from the `resolve()` method is a callable action representing the specific
action requested.  In addition to resolving class actions, the resolver can also resolve:

- Closures
- Functions

It is important to note that the behavior of these varies slightly.  Closures will be bound to
an empty `BaseController` and have all the available context in the same way as a normal controller,
i.e. either `$this->foo`, for example, or `$this['foo']`:

```php
$context = ['foo' => 'bar'];
$action  = $resolver->resolve(function() {
	return $this->foo;
}, $context);
```

If you provided for dependency injection (as shown above), a closure can also dynamically
resolve its function arguments:

```php
$context = ['foo' => 'bar'];
$action  = $resolver->resolve(function(Some\Namespace $provider) {
	return $provider->go($this->foo);
}, $context);
```

Regular functions cannot be bound nor executed the same way.  With this in mind, it is not possible
to do any comparable dependency injection or context setting.  Function actions are primarily to
support route mapping where routers could direct a request to a function's output.

### Calling the Action

Once an action is resolved, it can be executed directly:

```php
if ($action) {
	$value = $action();
}
```

Again, this will most likely be performed by your router, but the resolver will, either return
a callable action or `FALSE` if it cannot be resolved.

## Request / Response Mediation

The primary focus of a controller is to mediate between the incoming requests / data and the
handling services / views.  In order to help provide controllers with easy mechanisms for handling
common request analysis and response resolution, the `BaseController` implements a number of
methods and interfaces.

### Request Method Authorization

You can use `authorizeMethod()` to ensure that the current request method is supported by passing
an array or a single supported method:

```php
use IW\HTTP;

$this->authorizeMethod([HTTP\GET, HTTP\POST]);
```

This will also return the current request method as a shorthand, so you can use it in switch or
control statements directly:

```php
use IW\HTTP;

switch ($this->authorizeMethod([HTTP\GET, HTTP\POST])) {
	case HTTP\GET:

		//
		// Do things for get
		//

		break;

	case HTTP\POST:

		//
		// Do things for post
		//

		break;
}
```

If the method provided in the request is not authorized then the controller will automatically
reset the response body and status code to reflect a HTTP `405` and throw a
`Flourish\YieldException`.

### Accept Negotiation

The `Inkwell\Controller\NegotiatorInterface` provides a simple way to set negotiator objects for
the acceptable language and mime types.  It provides the following methods:

- `setLanguageNegotiator()`
- `setMimeTypeNegotiator()`

Each takes a single argument which is the negotiator.  The following example sets the negotiators
using [Will Durand's Negotiation library](https://github.com/willdurand/Negotiation):

```php
$language_negotiator = new Negotiation\LanguageNegotiator();
$mimetype_negotiator = new Negotiation\FormatNegotiator();

$controller->setLanguageNegotiator($language_negotiator);
$controller->setMimeTypeNegotiator($mimetype_negotiator);
```

#### Using Negotiation

The `BaseController` provides two methods to negotiate language and mime type similar to how
`authorizeMethod()` works when using the aforementioned negotiation library.

```php
$language = $this->acceptLanguage(['en', 'de']);
$mimetype = $this->acceptMimeType(['text/html', 'application/json']);
```

<div class="notice">
	<p>
		Note:  If you implement the `NegotiatorInterface` and/or use the `Negotiator` trait on your
		own controllers which do not extend `BaseController`, you will still need to implement
		your own methods to use them.
	</p>
</div>

As with the `authorizeMethod()` call, the above methods will set the appropriate response code and
throw `Flourish\YieldException` if no acceptable language or mime type are found.

## Return Values

Controllers can essentially return any value they like.  How the value is used or parsed will be
determined by your router, or potentially a gateway to your SAPI much lower down in the chain.
Since the response is usually provided by the context to the controller, you might modify some
aspects of it within the controller and just return the content to be used as the body.
