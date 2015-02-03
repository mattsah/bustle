<?php

	return Affinity\Config::create([
		//
		// The system php command
		//
		'php' => 'php',

		//
		// The document root
		//
		'docroot' => 'public',

		//
		// The execution mode determines default operation for some processes which should
		// naturally differ depending on the environment (development vs. production)
		//

		'execution_mode'  => IW\EXEC_MODE\DEVELOPMENT,

		//
		// Timezone
		//

		'timezone' => 'US/Pacific',

		//
		// Debugging information to use.  Keeping the debugging destination to NULL will
		// change the default depending on the execution mode.  If the execution mode
		// is IW\EXEC_MODE\DEVELOPMENT, it will dump errors to the screen.  Otherwise it will
		// use the configured $_SERVER['SERVER_ADMIN'] e-mail address.
		//

		'debugging' => [
			'destination' => NULL,
			'error_level' => E_ALL,
		],

		//
		// Session settings
		//

		'session' => [
			'name' => 'IWSESSID',
			'path' => sys_get_temp_dir()
		],

		//
		// The write directory provides a base directory to which the system and services can write
		// files to.  If some of these files need to be public, it is suggested you create relative
		// symbolic links in the "public" folder.
		//

		'write_directory' => 'writable'
	]);
