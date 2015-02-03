<?php

	return Affinity\Config::create(['providers', 'auth'], [
		'@providers' => [
			//
			// The provider mapping lists concrete class providers for given interfaces, the
			// interface is the key, while the class is the value.
			//

			'mapping' => [
				'iMarc\Auth\EntityInterface' => 'Inkwell\Auth\AnonymousUser'
			]
		],

		'@auth' => [

			//
			// Aliasing broader actions to more specific actions.  These are suggested action
			// names.
			//

			'aliases' => [
					'manage' => ['create', 'remove', 'update', 'select'],
					'admin'  => ['manage', 'permit']
			],

			//
			// Permissions define roles mapped to targets and the allowed actions on those
			// targets.  Traditionally, targets are class names so you can check for
			// premissions against an object, however, they can technically be arbitrary.
			//
			// Example (using above aliases):
			//
			// 'Admin' => [
			//     'User'    => ['admin'],
			//     'Article' => ['admin'],
			// ],
			// 'Editor' => [
			//     'Article' => ['manage']
			// ],
			// 'Subscriber' => [
			//     'Article' => ['select']
			// ]
			//

			'permissions' => [
				'Anonymous' => [

				]
			],

			//
			// Handlers allow for custom logic/callback overrides of how permissions are
			// determined.  The handler will be passed two arguments, the first is the auth
			// manager which can check ACLs and get the managed entity, the second will be
			// the object or string being checked.
			//
			// Example:
			//
			// 'User' => [
			//     'update' => 'Auth::userUpdate'
			// ]
			//
			// Example `Auth::userUpdate()` which would allow for a user to update
			// themselves:
			//
			// public function userUpdate($manager, $user)
			// {
			//     return $manager->has('update', $user) || $manager->entity == $user;
			// }
			//

			'handlers' => [

			]
		]
	]);
