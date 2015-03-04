# Available Components

As you probably gathered from the section about the [nano core](02-nano-core), inKWell is more
about helping you write the code you want in such a way that you can easily change behaviors, add
functionality, or create pluggable components.  That said, inKWell is not merely its core, and a
number of packages are already available for the most common development paradigms.

As with the core, functionality is just a quick composer call away.  For example:

```bash
composer require dotink/inkwell-events
```

Components provide classes, configs, and actions which allow you to easily add functionality to
inKWell for use in your application code.  Official components are considered part of the framework
but can be added or used on an as needed basis.  Currently available components include:

- Routing
- Controller
- Events
- View
- Auth
- ORM

Additional planned components are:

- Cache
- Logger

## Creating Custom Components

Creating inKWell components is easy.

The general operations are as follows:

- Create a composer package with an `src` directory to contain classes, interfaces, traits, etc.
- Add your default configuration(s) to `plugin/config/default` (use subdirectories for additional
  namespacing).
- Add your default bootstrap action(s) to `plugin/include/default`
- Then enable it as an opus-package in your `composer.json`:

```json
"type": "opus-package",
"extra": {
	"opus": {
		"inkwell/framework": {
			"plugin/config"  : "config",
			"plugin/include" : "include"
		}
	}
}
```

For a more complete tutorial using a real world example of packaging Doctrine 2 ORM for use
with inKWell, check out
["Creating a Component for Doctrine"](/docs/tutorials/01-creating-a-component-for-doctrine).

To understand more about configuration and bootstrap actions, try [the bootstrapping section of
the nano core docs](/docs/basics/02-nano-core#Bootstrapping).

Once you've got the package registered with [packagist](https://packagist.org/) simply run:

```bash
composer require package/name
```
