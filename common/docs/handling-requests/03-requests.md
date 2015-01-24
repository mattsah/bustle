# Requests (HTTP)

Requests are not a separate component, but are part of larger components which provide specific
input / output components for your application.  The most common request is HTTP requests.  This
is what is documented here.  If you're writing a command line application, check out
[the inKWell CLI component](../supplemental/01-cli).

## Installation

```bash
composer require dotink/inkwell-http
```

**NOTE**:  You do not need to explicitly install this package if you're using the inKWell routing
package.

## Providers

| Via                 | Description
|---------------------|-----------------------------------------------------
| `$app['request']`   | The original request made to the application
| `$app['gateway']`   | An HTTP gateway responsible for populating requests and rendering responses
