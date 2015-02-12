<?php

	return Affinity\Action::create(['core'], function($app, $broker) {

			//
			// Here we set up an `auth.init` callback which can be called later by whatever
			// action is responsible for getting our user.  It takes a single argument, which
			// is the user itself, and will register that with the auth provider.
			//
			// Whatever action is handling authentication should have `'auth'` listed as an
			// action dependency so this function is available.  Once the user is resolved
			// it can call it.
			//

			$app['auth.init'] = function($entity) use ($app, $broker) {

				$acls    = array();
				$manager = $broker->make('iMarc\Auth\Manager', [':entity' => $entity]);

				foreach ($app['engine']->fetch('@auth') as $id) {
					$acl              = $broker->make('iMarc\Auth\ACL');
					$aliases          = $app['engine']->fetch($id, '@auth.aliases',     array());
					$role_permissions = $app['engine']->fetch($id, '@auth.permissions', array());
					$target_handlers  = $app['engine']->fetch($id, '@auth.handlers',    array());

					foreach ($aliases as $alias => $actions) {
						$acl->alias($alias, $actions);
					}

					foreach ($role_permissions as $role => $permissions) {
						foreach ($permissions as $target => $actions) {
							$acl->allow($role, $target, $actions);
						}
					}

					foreach ($target_handlers as $target => $handlers) {
						foreach ($handlers as $action => $handler) {
							$manager->override($target, $action, $handler);
						}
					}

					$manager->add($acl);
				}

				$broker->share($app['auth'] = $manager);
			};

			//
			// Any object which is instantiated by our broker can implement the auth
			// `ConsumerInterface` to have the auth manager set on it post-instantiation.  In
			// most cases this will be controllers, but it could be other services.
			//

			$broker->prepare('Inkwell\Auth\ConsumerInterface', function($consumer, $broker) {
					$broker->execute([$consumer, 'setAuthManager']);
			});
	});
