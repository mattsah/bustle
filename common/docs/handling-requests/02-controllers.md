# Controllers

Because most people won't need custom resolution or highly specialized controllers, inKWell
provides a stock controller component which adds a base controller class as well as a standard
resolver for the router.

## Installation

```bash
composer require dotink/inkwell-controller
```

## Providers

| Via                         | Description
|-----------------------------|-----------------------------------------------------
| `$app['router.resolver']`   | A router resolver supporting controller insantiation, context filling, and closure encapsulation

## Overall Features

The controller component provides both a base controller class as well as a number of traits and
interfaces which allow you to plug in various additional pieces of supporting functionality.

<div class="notice">
	<p>
		With the exception of automatic constructor injection, all the features listed in this
		document are available for closures resolved using the controller packages resolver as
		well.  This is because the closure itself is wrapped in the `BaseController` and bound
		to it's scope.
	</p>
</div>

### Automatic Constructor Injection

The resolver provided for the base controller will use the dependency injector to automatically
resolve constructor dependencies based on class or interface typehints.  You can then assign these
to internal properties or perform additional actions on construct.

```php
private $object = NULL;

public function __construct(Custom\Class $object)
{
	$this->object = $object;
}
```

### Contextual Container

Additionally, the base controller acts as a publicly accessible container.  All container properties
are stored in an itnernal `context` property, so you don't need to worry about it conflicting with
private or protected properties.

You can get or set from the context using standard property getting/setting due to `__get()` and
`__set()` implementation.

```php
//
// Add to context
//

$this->view = $view;
```

Alternatively, you can access the context using `ArrayAccess`:

```php
//
// Add to context
//

$this['view'] = $view;
```

### Method Authorization

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

### Traits

The following traits are implemented by the `Inkwell\Controller\BaseController`.  Each has a
corresponding interface and may have supporting packages and actions to bootstrap them.

#### Negotiator

The `Inkwell\Controller\NegotiatorInterface` provides a simple way to set negotiator objects for
the acceptable language and mime types.  It provides the following methods:

- `setLanguageNegotiator()`
- `setMimeTypeNegotiator()`

Each takes a single argument which is the negotiator.  The base controller uses the
[Aura Accept](https://github.com/auraphp/Aura.Accept) package for negotiation.  And sets them up
in the `include/default/controller.php` action:

```php
$broker->prepare('Inkwell\Controller\NegotiatorInterface', function($controller, $broker) {
	$controller->setLanguageNegotiator($broker->create('Aura\Accept\Language\LanguageNegotiator'));
	$controller->setMimeTypeNegotiator($broker->create('Aura\Accept\Media\MediaNegotiator');
});
```

<div class="notice">
	<p>
		Note:  If you implement the `NegotiatorInterface` and/or use the `Negotiator` trait on your
		own controllers which do not extend `BaseController`, you will still need to implement
		methods to use them.
	</p>
</div>

The `BaseController` provides two methods for determining the best language and mime type.  Similar
to the `authorizeMethod()` call we showed earlier, you can pass an array of acceptable values and
it will return the best match, or [demit](01-routing#demit):

```php
$language = $this->acceptLanguage(['en', 'de']);
$mimetype = $this->acceptMimeType(['text/html', 'application/json']);
```

### Return Values

The controller actions are responsible for returning the content of the response or a response
directly.  You can modify the `$this->response` object directly, return a completely new response,
or just return content which will be embedded in the current response.
