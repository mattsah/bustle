<?php

	return [

		//
		// UTILITY SHORTHANDS
		//

		'LB' => PHP_EOL,
		'DS' => DIRECTORY_SEPARATOR,

		//
		//
		//
		'ERROR\DESTINATION_NULL'     => 'null',
		'ERROR\DESTINATION_EMAIL'    => 'email',
		'ERROR\DESTINATION_RESPONSE' => 'response',

		//
		//
		//
		'EXEC_MODE\PRODUCTION'  => 'production',
		'EXEC_MODE\DEVELOPMENT' => 'development',

		//
		// HTTP METHODS
		//

		'HTTP\GET'    => 'GET',
		'HTTP\POST'   => 'POST',
		'HTTP\PUT'    => 'PUT',
		'HTTP\DELETE' => 'DELETE',
		'HTTP\HEAD'   => 'HEAD',

		//
		// HTTP RESPONSES
		//

		'HTTP\OK'                   => 'Ok',
		'HTTP\CREATED'              => 'Created',
		'HTTP\ACCEPTED'             => 'Accepted',
		'HTTP\NO_CONTENT'           => 'No Content',
		'HTTP\BAD_REQUEST'          => 'Bad Request',
		'HTTP\NOT_AUTHORIZED'       => 'Not Authorized',
		'HTTP\FORBIDDEN'            => 'Forbidden',
		'HTTP\NOT_FOUND'            => 'Not Found',
		'HTTP\NOT_ALLOWED'          => 'Not Allowed',
		'HTTP\NOT_ACCEPTABLE'       => 'Not Acceptable',
		'HTTP\UNSUPPORTED_MIMETYPE' => 'Unsupported Media Type',
		'HTTP\SERVER_ERROR'         => 'Internal Server Error',
		'HTTP\UNAVAILABLE'          => 'Service Unavailable',

		//
		// HTTP REDIRECTS
		//

		'HTTP\REDIRECT_INTERNAL'  => 302, // Found is used for internal redirects (aka: rewrites)

		'HTTP\REDIRECT_PERMANENT' => 301, // Redirects permanently with get method
		'HTTP\REDIRECT_SEE_OTHER' => 303, // Redirects with get method, assuming processing done
		'HTTP\REDIRECT_TEMPORARY' => 307, // Redirects with original method, assuming nothing done

		//
		// CACHE TYPES
		//

		'CACHE\PUBLIC'   => 'public',
		'CACHE\PRIVATE'  => 'private',
		'CACHE\NO_STORE' => 'no-store',

		//
		// REGEX
		//

		'REGEX\ABSOLUTE_PATH' => '#^(/|\\\\|[a-z]:(\\\\|/)|\\\\|//)#i'
	];
