# Requests (HTTP)

Requests are not a separate component, but are part of a larger component which provides specific
input / output components for your application.  The most common request is HTTP requests.  This
is what is documented here.

Requests are designed for both receiving (from clients as a server) and sending (as client to a
server).

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

## Instantiation

Although you can instantiate an empty request and work with it directly, in most cases you're
going to want your request to be populated from the data provided by the SAPI.  In order to do
this you can create a gateway server and populate the request.

```php
use Inkwell\HTTP;

$request = new HTTP\Resource\Request();
$gateway = new HTTP\Gateway\Server();

$gateway->populate($request);
```

## Getting / Setting

There are a number of properties you can get/set on the request itself or on components which
are directly relatedo the request.

### URLs

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

URLs are immutable, so if you need to modify the URL on the request, you need to make sure you
set it to the returned URL:

```php
$new_url = $this->request->getUrl()->modify('/new/path.html');

$this->request->setURL($new_url);
```

### HTTP Method

Get the method:

```php
$method = $request->getMethod();
```

Check the method:

```php
use IW\HTTP;

if ($request->checkMethod(HTTP\POST)) {
	//
	// Do some posting
	//
}
```

Set the method:

```php
use IW\HTTP;

$request->setMethod(HTTP\GET);
```

### HTTP Headers

You can get or set headers on a request by working with the headers property which is populated
as an instance of `Dotink\Flourish\Collection`, see:
[https://github.com/dotink/flourish-collection](https://github.com/dotink/flourish-collection).

```php
$accept = $this->request->headers->get('Accept');
```

You can get all headers as an array via:

```php
$headers = $this->request->headers->get()
```

Set a specific header by doing:

```php
$this->request->headers->set('X-Forwarded-For', $ip_address);
```

Set multiple headers with an array:

```php
$this->request->headers->set([
	'Accept-Language' => $this->request->cookies->get('lang'),
	'X-Custom-Header' => $value
]);
```

### Parameter Data

Get data:

```php
$name = $this->request->params->get('name');
```

Provide a default:

```php
$page = $this->request->params->get('page', 1);
```

Get all the data:

```php
$params = $this->request->params->get();
```

Set data:

```php
$this->request->params->set('task', $next_task->getId());
```
