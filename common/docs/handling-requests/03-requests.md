# Requests (HTTP)

Requests are not a separate component, but are part of larger components which provide specific
input / output components for your application.  The most common request is HTTP requests.  This
is what is documented here.  If you're writing a command line application, check out
[the inKWell CLI component](../supplemental/01-cli).

## Installation

```bash
composer require dotink/inkwell-http
```

<div class="notice">
	<p>
		You do not need to explicitly install this package if you're using the inKWell routing
		package.
	</p>
</div>

## Providers

| Via                 | Description
|---------------------|-----------------------------------------------------
| `$app['request']`   | The original request made to the application
| `$app['gateway']`   | An HTTP gateway responsible for populating requests and rendering responses

The HTTP component will only register the above providers in the event that the application is
accessed via HTTP based SAPIs.

## Accessing the request

In both closures and controllers, the request object can be accessed using `$this->request`.
Without a configured resolver, closures our bound to the router which has access to the request
object directly.

## Parameters

Parameters are available on the `params` object which is a simple collection:

```php
$name = $this->request->params->get('name');
```

The params object contains parameters parsed from the route as well as the traditional `$_GET` and
`$_POST` super global values.  

<div class="notice">
	<p>
		Parameters which are parsed from routes are added using the `set()` method, so you cannot
		access them via `$_GET` or `$_POST` directly, however, these values are not cleared so
		any standard data which would be available in them is.
	</p>
</div>

If you need to set a parameter for a subrequest or later method call, you can use `set()`:

```php
$this->request->params->set('foo', 'bar');
```

## Getting the URL

The URL is held in a URL object which provides a number of additional methods, see:
[https://github.com/dotink/flourish-url](https://github.com/dotink/flourish-url).  To get the URL
object you need to use the `getURL()` method:

```php
$url = $this->request->getURL();
```

Additionally there are methods on the URL object for retrieving separate pieces:

```php
$host = $this->request->getURL()->getHost();
$path = $this->request->getURL()->getPath();
```

You can additionally modify the URL rather easily for redirection or other purposes:

```php
$url = $this->request->getURL();

if ($url->getScheme() != 'https') {
	$this->response->setStatusCode(301);
	$this->response->headers->set('Location', $url->modify(['scheme' => 'https']));

	return $this->response;
}
```

The above would immediately redirect to the HTTPS version of the URL by replacing the scheme only.

## Getting Headers

As with the previous example on the response, you can get the request headers via the `headers`
property which, similar to `params`, represents a simple collection:

```php
$accept = $this->request->headers->get('Accept');
```
