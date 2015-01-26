# Installation of inKWell

As with any modern framework, you can use [composer](https://getcomposer.org) to get yourself
started quickly and easily.

```bash
composer create-project -s dev inkwell/framework <target>
```

## Why Development Stability?

The inKWell framework is currently in a prolonged beta.  Although it's unlikely that developer
facing functionality will break, there's still some work being done on how certain components
interact.  We encourage users to submit [issues on GitHub](https://github.com/dotink/inkwell-framework/issues)
for any of the documented functionality if we break it.

## Server Setup

Out of the box, inKWell will not provide any `index.php` entry point although it will provide an
apache `.htaccess` file so you can easily add one.  The default docroot for an inKWell project is
the `public` folder in the application root.  This leaves all your classes, configuration, etc,
back one directory.

In addition to an `.htaccess` file a comparable `.user.ini` file is there for CGI or FPM setups.

<div class="notice">
	<p>
		If you choose to use inkwell's official components rather than just using the nano core,
		we suggest you skip ahead right to the
		<a href="../handling-requests/01-routing">routing documentation</a>.
	</p>
</div>

Once you have your server pointing at your document root and/or an `index.php` file in the document
root, you can look at the [nano core](02-nano-core) documentation in order to understand how to
bootstrap your application.
