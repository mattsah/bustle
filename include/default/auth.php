<?php

	return Affinity\Action::create(['core'], function($app, $broker) {

			//
			// Whatever is handling login should require ['auth'] as a dependency and then run
			// $app['auth.init']($user) initialize the user it has resolved.
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

			$broker->prepare('Inkwell\Auth\ConsumerInterface', function($consumer, $broker) {
					$broker->execute([$consumer, 'setAuthManager']);
			});
	});
