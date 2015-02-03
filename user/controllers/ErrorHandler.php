<?php

	use IW\HTTP;
	use Inkwell\View;
	use Inkwell\Controller;
	use Inkwell\Security\UserProviderInterface;
	use Dotink\Flourish\Message;
	use iMarc\Auth\Manager;

	class ErrorHandler extends Controller\BaseController
	{
		const MSG_LOGIN           = 'You must be logged in to access that resource.';
		const MSG_LOGIN_DIFFERENT = 'You do not have permissions to access that resources, try logging in as a different user.';


		/**
		 *
		 */
		public function __construct(View $view, Message $message, Manager $auth, UserProviderInterface $user_provider)
		{
			$this->view         = $view;
			$this->message      = $message;
			$this->auth         = $auth;
			$this->userProvider = $user_provider;
		}


		/**
		 * Handles resources which are forbidden to the current user
		 *
		 */
		public function forbidden()
		{
			$this->userProvider->setLoginRedirect(
				$this->auth->entity,
				$this->request->getURL()->getPathWithQuery()
			);

			if ($this->auth->is('Anonymous')) {
				$this->message->create('error', self::MSG_LOGIN);
			} else {
				$this->message->create('error', self::MSG_LOGIN_DIFFERENT);
			}

			$this->router->redirect('/login', HTTP\REDIRECT_SEE_OTHER, FALSE);
		}
	}
