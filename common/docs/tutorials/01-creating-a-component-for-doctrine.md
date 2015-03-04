# Creating a Component for Doctrine

In this tutorial we will create a re-usable inKWell component that enables us to easily integrate
[Doctrine 2 ORM](http://www.doctrine-project.org/projects/orm.html) into our application.

## Create the Package Structure

The first step we'll want to do is to create a git repository for the package.  This can be anywhere
you like, but in my case, since I keep all my inKWell packages in one place, I'm going to create
mine in `~/Code/dotink/inkwell-packages/doctrine`:

```bash
git init ~/Code/dotink/inkwell-packages/doctrine
```

Then change to that directory:

```bash
cd ~/Code/dotink/inkwell-packages/doctrine
```

### Adding Common Directories

I'm going to make the following directory structure right off the bat:

```bash
.
└── plugin
    ├── config
    │   └── default
    └── include
  	  └── default
```

To do this I can just execute `mkdir` with the `-p` argument:

```bash
mkdir -p plugin/{config,include}/default
```

### Adding a Composer File

Next, I'm going to want to add my `composer.json`.  According to Doctrine's getting started page,
I'll want the following:

```json
{
	"require": {
		"doctrine/orm": "2.4.*",
		"symfony/yaml": "2.*"
	},
	"autoload": {
		"psr-0": {"": "src/"}
	}
}
```

I'm going to tweak this a little for my own personal preferences, but also to add additional
information for the package itself:

```json
{
	"name": "dotink/inkwell-doctrine",
	"description": "Doctrine 2 Integration for inKWell",
	"keywords": ["orm", "database", "doctrine"],
	"license": "AGPL-3.0",
	"authors": [
		{
			"name": "Matthew J. Sahagian",
			"email": "matt@imarc.net",
			"role": "Developer"
		}
	],
	"require": {
		"php": ">=5.4.0",
		"doctrine/orm": "2.4.*",
		"symfony/yaml": "2.*"
	}
}
```

In the above, I've just added some developer, licensing, and meta info, and removed the autoloader
since I don't expect to have any additional classes just yet.  Let's make sure the composer file
is valid:

```bash
composer validate
```

Looks good.

## Adding Bootstrapping

Doctrine has several ways that its entity configuration can be done.  In order to allow for the
most amount of flexibility, we're going to allow any of them.  To do this, we're going to add both
a configuration for inKWell, as well as a bootstrap action.

### Bootstrap Configuration

Let's go ahead an create a configuration file that'll allow us to switch them:

```php
<?php

	return Affinity\Config::create([

		//
		// The configuration type determines how Doctrine's entity configuration is done.
		// Possible values include:
		//
		// - annotations (config will be read from annotations on classes in your `entity_root`)
		// - yaml		(config will be read from YAML files in your `config_root`)
		// - xml		 (config will be read from XML files in your `config_root`)
		//

		'config_type' => 'annotations',

        //
        // The `entity_root` and `config_root` will be relative to your inKWell application
        // root unless you specify an absolute path (preceded with '/').
        //

		'entity_root' => 'user/entities',

		'config_root' => 'config/default/doctrine/entities'

	]);
```

<div class="notice">
    <p>
        Because the affinity bootstrapper uses PHP for configurations, you can feel free to
        comment as much as you like.  This is often useful for telling people what the available
        options for a given value are.
    </p>
</div>

Let's save this in our package, but before we do let's make an additional sub-directory so that
we have all of our Doctrine configs namespaced:

```bash
mkdir plugin/config/default/doctrine
```

Now let's save our config to `plugin/config/default/doctrine/entities.php`.

### Bootstrap Action

In order to make the above configuration useful, we're going to want to add an action which can
take our inKWell configuration and execute the requisite logic to set things up.  In this case,
we'll begin by setting up an action that determines the configuration type for Doctrine and
sets it up accordingly:

```php
<?php

	use Dotink\Flourish;
	use Doctrine\ORM\Tools\Setup;

	return Affinity\Action::create(['core'], function($app, $broker) {
		$dev_mode = $app->checkExecutionMode(IW\EXEC_MODE\DEVELOPMENT);

		extract($app['engine']->fetch('doctrine/entities', [
			'config_type' => 'annotations',
			'entity_root' => 'user/entities',
			'config_root' => 'config/default/doctrine/entities'
		]));

		switch ($config_type) {
			case 'annotations':
				$root   = $app->getDirectory($entity_root);
				$config = Setup::createAnnotationMetadataConfiguration([$root], $dev_mode);
				break;

			case 'yaml':
				$root   = $app->getDirectory($config_root);
				$config = Setup::createXMLMetadataConfiguration([$root], $dev_mode);
				break;

			case 'xml':
				$root   = $app->getDirectory($config_root);
				$config = Setup::createYAMLMetadataConfiguration([$root], $dev_mode);
				break;

			default:
				throw new Flourish\ProgrammerException(
					'Unsupported doctrine configuration type "%s"',
					$config_type
				);
		}

		$app['entity.config'] = $config;
	});
```

Now that we have that done, let's save it in `plugin/include/default/doctrine.php`.  We're going
to add a bit more to it later to set up our entity manager, but let's dissect what we have so far.

Our action begins with:

```php
return Affinity\Action::create(['core'], function($app, $broker) {
	...
```

This says that we want to create an affinity bootstrapper action which:

1. Requires a `core` module to be executed first, this is the inKWell core bootstrapping.
2. Will execute the logic inside the Closure accepting the application and broker parameters.

The `$app` and `$broker` are provided by inKWell to affinity at startup time.  The `$app` is
the inKWell core itself (an application container with some helper methods) and the `$broker` is
the shared instance of our dependency injector.  These will be provided to any bootstrapping
actions you create.

Next, we determine if our application is in development mode since Doctrine is set up to behave a
bit differently towards entity configuration depending on the mode:

```php
$dev_mode = $app->checkExecutionMode(IW\EXEC_MODE\DEVELOPMENT);
```

We use the `checkExecutionMode()` helper method on the `$app` to do this.  This will compare the
configured execution mode to the value we pass, in this case `IW\EXEC_MODE\DEVELOPMENT`.

<div class="notice">
    <p>
        All constants in the `IW` namespace are configured via the `constants.php` file in the root
        of and inKWell project.  These should not be modified by the user, but can be used for
        consistency across inKWell components and applications.
    </p>
</div>

From there, we extract our configuration data.

```php
extract($app['engine']->fetch('doctrine/entities', [
	'config_type' => 'annotations',
	'entity_root' => 'user/entities',
	'config_root' => 'config/default/doctrine/entities'
]));
```

You don't need to use extract, however, it is useful when there are number of properties in the
same configuration which you may need access to all at once.  The `$app['engine']` key refers to
the affinity bootstrapper itself and its `fetch()` method is what is used to get configuration
values.

In this case, since we're using `extract()` to extract them into the current scope, we provide
only the specific ID of the config `doctrine/entities` and an array of the configuration data
names (as keys) to the default values (as the values).

To setup the proper Doctrine configuration, we use a simple `switch()` construct to execute the
configuration appropriate class and method per Doctrine's docs and depending on the type.

```php
switch ($config_type) {
	case 'annotations':
		$root   = $app->getDirectory($entity_root);
		$config = Setup::createAnnotationMetadataConfiguration([$root], $dev_mode);
		break;

		...
```

Using the `getDirectory()` helper method on the `$app` instance we ensure that we have an
absolute path to the root directory to pass to Doctrine's config.   This method will take our
relative configured root, and figure out where it is with respect to the project's application
root.

Lastly, if we haven't thrown an exception due to an unsupported type, we register the entity
configuration in our container:

```php
$app['entity.config'] = $config;
```

## Packaging for Distribution

Although we've yet to complete the configuration, we're in a good position now to begin setting
up our package for distribution and doing some initial testing.

Thus far we have:

- Created a package with our requisite dependencies
- Set up and initial inKWell configuration to specify entity configuration information
- Set up and initial inKWell bootstrap action to create the Doctrine entity configuration

In order to ensure our configuration and action are installed properly when we use the
package, we're going to add some additional composer information, namely the following pieces
of information:

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

<div class="notice">
	<p>
		Opus is a composer plugin which will allow us to copy our default configuration and action
		into our project `config` and `include` folders when we install it via composer.
    </p>
</div>

Once we have this, let's go ahead and do our initial commit.

```bash
git add .
git commit -m "Initial Commit"
```

### Testing the Package

Once we have a commit, we can test the package on a project.  The following assumes at a minimum
that we have installed inKWell's nano core and have the `dotink/inkwell-console` package installed
as well.

Since we haven't made this package public or pushed it to any public composer repositories yet,
let's take a moment to add our repository to our project's `composer.json` (not our packages).

We'll already have the inKWell composer repository in this section, so we'll just append our
custom package's to the array:

```json
"repositories": [
	{
		"type": "composer",
		"url": "http://packages.dotink.org"
	},
	{
		"type": "vcs",
		"url": "/home/matt/Code/dotink/inkwell-packages/doctrine"
	}
]
```

Once added, we can go ahead and run:

```bash
composer require dotink/inkwell-doctrine
```

You should note when it actually installs our package (not just the Doctrine deps) a line such as
`Copying files from /vendor/dotink/inkwell-doctrine`.  This is Opus copying the config and
action files into our project.

#### Using Quill

In order to test this, we're quickly going to use the inKWell console (called quill).  If you
don't have this installed, you can install it via:

```bash
composer require dotink/inkwell-console
```

To run it, we're simply going to run:

```bash
php bin/quill
```

In doing this, you should immediately see the following error (except with whatever path you're
using to your project):

_Dotink\Flourish\ProgrammerException: Could not access directory
"/home/matt/Projects/iw.quickstart/user/entities" in file
/home/matt/Projects/iw.quickstart/vendor/dotink/inkwell-core/src/Core.php on line 60_

This error is because in our bootstrap action we requested an application directory that didn't
exist.  Despite seeing this error, it is a good sign since it is looking for the directory which
we want Doctrine's entity configuration to use by default.

Let's go ahead and create the directory then rerun quill.  From our project root:

```bash
mkdir user/entities
php bin/quill
```

This time, you should see a prompt that looks like `[@]>>>`.  The `@` is similar to `~` on unix
systems, but instead of indicating that you're in your home directory, it's indicating that
you're in your project root.  The `>>>` is simply saying that the console is ready for input
and is currently not in an open block (like a foreach loop).

All we're going to do at this stage is test whether or not our entity configuration is properly
registered on the application container:

```
[@]>>> $app['entity.config']
=> <Doctrine\ORM\Configuration #000000005fe4c10d00000000068cc762> {}
```

Looks like we got it, but since our entity configuration alone isn't very useful, let's put
the finishing touches on getting the entity manager available.

## Additional Functionality

Since our entity configuration alone isn't very useful, let's go ahead and make our package able
to bootstrap the entity manager as a whole.  This will require some additional connnection
information which we'll also want to put into a configuration.

### Add Connection Configuration

Return to the package root and add a *new* configuration file at
`plugin/config/default/doctrine/connection.php`:

```php
<?php

	return Affinity\Config::create([

		//
		// A NULL type will indicate that no database is configure. Common drivers include
		// `pdo_mysql`, `pdo_pgsql`, `pdo_sqlite`, for a complete list see:
		// http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#driver
		//

		'driver'   => NULL,

		//
		// You should set these in your environment so that they can change more easily if the app
		// is deployed in many places or with different settings.
		//

		'host'	 => $app->getEnvironment('DB_HOST', 'localhost'),
		'dbname'   => $app->getEnvironment('DB_NAME', NULL),
		'user'	 => $app->getEnvironment('DB_USER', NULL),
		'password' => $app->getEnvironment('DB_PASS', NULL),
	]);
```

### Making the Connection

Although we made our connection configuration separate, we're going to bootstrap the connection
in the same action as our entity configuration.  We want to make sure if the database is not
configured we don't bother with any of it, so, beginning at the beginning of our previously
created `plugin/include/default/doctrine.php` callback, let's start adding the following:

```php
$connection_settings = $app['engine']->fetch('doctrine/connection');

if (!isset($connection_settings['driver'])) {
	return;
}
```

The above will mean that by default, with no connection configured, we're not going to execute
any additional Doctrine bootstrapping code.

Additionally, at the very top of the file, let's add a use statement for our entity manager:

```php
use Doctrine\ORM\EntityManager;
```

Now, for the very last line of the callback, following the Doctrine docs, let's set up our entity
manager:

```php
$app['entity.manager'] = EntityManager::create($connection_settings, $config);
```

Finally, just below that, we want to share our entity manager via the `$broker` which is our
dependency manager.  This will allow controllers and services to have our configured entity
manager automatically injected:

```php
$broker->share($app['entity.manager']);
```

Let's go ahead and commit that:

```php
git add plugin/config/default/connection.php
git commit -a -m 'Enable connection setup'
```

If we return to our project folder and run a `composer update` we can get the changes for testing.
Let's begin by removing our previously created `user/entities` folder and re-running quill:

```bash
rmdir user/entities
php bin/quill
```

Notice our error has disappeared.  This is because by default, we're not doing anything if we
haven't set up any connection.  Let's move on to documentation and final testing.

## Documenting

The simplest way to document our package will be to add a simple `README.md` file.  I'll be adding
some additional files to my version including a `LICENSE.md`, a `lab.config`, and a `sage.config`
as these may come in handy later.

The README will cover the basic installation and setup which will show:

- How to require the package with composer
- Where to configure the database connection and entity configuration
- The step to create our entity configuration directory

You can see the final result here:
[https://github.com/dotink/inkwell-doctrine/blob/2f28479a2ab9752b0020b5075c734e9ad41a47f8/README.md](https://github.com/dotink/inkwell-doctrine/blob/2f28479a2ab9752b0020b5075c734e9ad41a47f8/README.md)

## Publishing and Final Testing

To complete our testing, we'll want to publish our package completely set up a database and use
quill to make sure our entity manager is setup.

To keep this concise, I won't go into details about the possible databases and how to configure
those, but rather just show what it looks like with my current setup.

To publish I will want to create a new repository on github.  Once this is complete github gives
me the commands necessary to push my package upstream:

```bash
git remote add origin git@github.com:dotink/inkwell-doctrine.git
git push -u origin master
```

After I've done this I can go to [packagist](https://packagist.org/) and register the package by
loggin in, clicking on submit, and pasting my package URL:
`https://github.com/dotink/inkwell-doctrine`

Returning to my original test project, I'm going to remove the local repository entry that looked
like this:

```json
{
	"type": "vcs",
	"url": "/home/matt/Dropbox/Code/dotink/inkwell-packages/doctrine"
}
```

Then rerun composer update:

```bash
composer update
```

This should now be pulling the package as registered with packagist.  Note you may want to set up
a github webhook with packagist to keep your package up to date.

I'll go ahead and create a simple database for testing:

```bash
echo "CREATE DATABASE test WITH OWNER web;" | psql -U postgres
```

Now configure the package.  I'm going to choose YAML configuration so I will do the following
in `config/default/doctrine/entitie.php`:

```php
'config_type' => 'yaml',
```

I'll keep the default `config_root` so all my configurations stay together, but I'll make
sure I create the directory:

```php
mkdir -p config/default/doctrine/entities
```

Lastly, I'll configure my connection in `config/default/doctrine/connection.php`:

```php
'driver'   => 'pdo_pgsql',
'host'	   => $app->getEnvironment('DB_HOST', 'localhost'),
'dbname'   => $app->getEnvironment('DB_NAME', 'test'),
'user'	   => $app->getEnvironment('DB_USER', 'web'),
'password' => $app->getEnvironment('DB_PASS', NULL),
```

Note, I've modified the default values rather than removing the `getEnvironment()` call directly.
This will enable me to still override these settings via the environment, but fall back on these
defaults for my local development.

Finally, I should be able to execute quill and get my `$app['entity.manager']`:

```bash
php bin/quill
```

```php
[@]>>> $app['entity.manager']
=> <Doctrine\ORM\EntityManager #000000001ff2ac8a0000000040d70904> {}
```

You can see the final package availabe on packagist here:
[https://packagist.org/packages/dotink/inkwell-doctrine](https://packagist.org/packages/dotink/inkwell-doctrine)

Or, if you want to fork it and start adding more improvement, check it out on github:
[https://github.com/dotink/inkwell-doctrine](https://github.com/dotink/inkwell-doctrine)
