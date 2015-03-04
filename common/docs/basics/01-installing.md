# Installation of inKWell

As with any modern framework, you can use [composer](https://getcomposer.org) to get yourself
started quickly and easily.

```bash
composer create-project -s dev inkwell/framework <target>
```

## Why Development Stability?

The inKWell framework is currently in a prolonged beta.  Although it's unlikely that developer
facing functionality will break, there's still some work being done on how certain components
interact.  We encourage users to submit any issues in the core or components to the framework
[issue tracker on GitHub](https://github.com/dotink/inkwell-framework/issues).

## Server Setup

The default docroot for an inKWell project is the `public` folder in the application root.  This
leaves all your classes, configuration, etc, back one directory.

Out of the box, inKWell's nano core will not provide any `index.php` entry point although it will
provide an apache `.htaccess` file so you can easily add one.

In addition to an `.htaccess` file a comparable `.user.ini` file is there for CGI or FPM setups.

<div class="notice">
	<p>
		The <a href="../handling-requests/01-routing">inkwell-router</a> package <strong>does
		provide an index.php</strong> which will be copied on installation and works directly
		with that router.  Even if you don't intend to use the official routing component, it
		may be of some value to look at.
	</p>
</div>

### Make Writable Writable

You will want to make writable owned by the same user or group as the web server.  On debian
servers, this user is often `www-data`, so for example you might run:

```bash
chown www-data <app_root>/writable
```

### Post Setup

<div class="notice">
	<p>
		If you choose to use inkwell's official components rather than just using the nano core,
		we suggest you check out the <a href="../quick-start">Quick Start Guide</a> instead.
	</p>
</div>

Once you have your server pointing at your document root and/or an `index.php` file in the document
root, you can look at the [nano core](02-nano-core) documentation in order to understand how to
bootstrap your application.
