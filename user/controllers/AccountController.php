<?php namespace Inkwell\Security
{
	use IW\HTTP;
	use Inkwell\Auth;
	use Inkwell\View;
	use Inkwell\Controller;
	use Dotink\Flourish\Message;

	use JWT;
	use SignatureInvalidException;
	use Exception;

	class AccountController extends Controller\BaseController implements Auth\ConsumerInterface
	{
		use Auth\ControllerConsumer;

		const MSG_LOGIN            = 'You must be logged in to access that resource.';
		const MSG_LOGIN_DIFFERENT  = 'You do not have permissions to access that resources, try logging in as a different user.';
		const MSG_INCORRECT_USER   = 'Your username appears to be incorrect';
		const MSG_INVALID_TOKEN    = 'Your token is invalid or expired, please try joining again';
		const MSG_INVALID_EMAIL    = 'The e-mail you submitted is invalid for the original token';
		const MSG_INVALID_PASSWORD = 'The password you supplied was incorrect, please try again';

		/**
		 * The user's login (generally e-mail, but can be whatever)
		 *
		 * @var string
		 */
		private $login = NULL;


		/**
		 * The user provider responsible for actually doing user work
		 */
		private $userProvider = NULL;


		/**
		 *
		 */
		public function __construct(View $view, Message $message, UserProviderInterface $user_provider = NULL)
		{
			$this->view         = $view;
			$this->message      = $message;
			$this->userProvider = $user_provider;
		}


		/**
		 *
		 */
		public function __prepare($action, $context = array())
		{
			parent::__prepare($action, $context);

			if (!$this->userProvider && $action != 'forbidden') {
				$this->response->setStatus(HTTP\NOT_FOUND);
				$this->router->defer(NULL);
			}

			$this->view->load('account/' . $action . '.html');
			$this->view->set([
				'error'   => $this->message->create('error'),
				'success' => $this->message->create('success')
			]);
		}


		/**
		 * Handles resources which are forbidden to the current user
		 *
		 */
		public function forbidden()
		{
			if ($this->userProvider) {
				$this->userProvider->setLoginRedirect(
					$this->auth->entity,
					$this->request->getURL()->getPathWithQuery()
				);

				if ($this->auth->is('Anonymous')) {
					$this->message->create('error', self::MSG_LOGIN);
				} else {
					$this->message->create('error', self::MSG_LOGIN_DIFFERENT);
				}

				return $this->router->redirect('/login', HTTP\REDIRECT_SEE_OTHER, FALSE);
			}

			return 'Forbidden';
		}


		/**
		 *
		 */
		public function join()
		{
			if ($this->request->checkMethod(HTTP\POST)) {
				$token = JWT::encode([
					'name'  => $this->request->params->get('name'),
					'email' => $this->request->params->get('email')
				], session_id());

				$this->message->create('success', sprintf(
					'<a href="%s">Complete your registration now</a>',
					$this->request->getUrl()->modify(['path'  => '/register', 'query' => [
						'token' => $token
					]])
				));
			}

			return $this->view;
		}


		/**
		 * Logs a user into the system using the user provider
		 *
		 * @access public
		 * @return mixed|Response The user object or response object depending on how accessed
		 */
		public function login()
		{
			//
			// Try to get the user from a cookie
			//

			$user = $this->getUserFromCookie();

			//
			// If we're not the entry action, we're being used to get the logged in user
			//

			if (!$this->router->isEntryAction([$this, __FUNCTION__])) {
				return $user ?: $this->userProvider->getUser();
			}

			//
			// If we already have a user, then they're logged in, don't show them the login page
			//

			if ($user) {
				$this->completeLogin($user);
			}

			//
			// Actually try to log a user in
			//

			if ($this->request->checkMethod(HTTP\POST)) {
				$login    = $this->request->params->get('login');
				$password = $this->request->params->get('password');
				$user     = $this->userProvider->getUser($login);

				if (!$user) {
					$error = $this->message->create('error', self::MSG_INCORRECT_USER);

				} elseif (!$this->userProvider->verifyPassword($user, $password)) {
					$error = $this->message->create('error', self::MSG_INVALID_PASSWORD);

				} else {
					$this->completeLogin($user);

				}
			}

			return $this->view;
		}


		/**
		 *
		 */
		public function logout()
		{
			$user = $this->getUserFromCookie();

			$this->revokeUserCookie();

			$this->response->setStatusCode(HTTP\REDIRECT_SEE_OTHER);
			$this->response->headers->set('Location', $this->request->getURL()->modify(
				$this->userProvider->getLogoutRedirect($user)
			));


			return $this->response;
		}


		/**
		 *
		 */
		public function register()
		{
			$token = $this->request->params->get('token', NULL);

			if (!$token) {
				$this->router->redirect('/join');

			}  else {

				try {
					$data = (array) JWT::decode($token, session_id());

				} catch (SignatureInvalidException $e) {
					$this->message->create('error', self::MSG_INVALID_TOKEN);
					$this->router->redirect('/join');
				}

				if ($this->request->checkMethod(HTTP\POST)) {
					if ($data['email'] != $this->request->params->get('email')) {
						$this->message->create('error', self::MSG_INVALID_EMAIL);
					}

					$data = array_merge($data, $this->request->params->get());

					try {
						$user = $this->userProvider->createUser($data);

						$this->completeLogin($user);

					} catch (Exception $e) {
						$this->message->create('error', $e->getMessage());
					}
				}

				$this->view->set($data);
			}

			return $this->view;
		}


		/**
		 *
		 */
		public function refresh()
		{
			if (!$this->login || $this->response->cookies->has('security_user')) {
				return;
			}

			session_regenerate_id(TRUE);

			$this->response->cookies->set('security_user', [
				'login' => $this->login,
				'token' => session_id(),
				'limit' => strtotime('+30 minutes')
			]);
		}


		/**
		 *
		 */
		private function completeLogin($user)
		{
			$this->login = $this->userProvider->getLogin($user);

			$this->refresh();

			$this->response->setStatusCode(HTTP\REDIRECT_SEE_OTHER);
			$this->response->headers->set('Location', $this->request->getURL()->modify(
				$this->userProvider->getLoginRedirect($user)
			));

			$this->router->demit(NULL);
		}


		/**
		 *
		 */
		private function getUserFromCookie()
		{
			$user = $this->request->cookies->get('security_user');

			if (!$user || $user->limit < time() || session_id() != $user->token) {
				return NULL;

			} else {
				$this->login = $user->login;
			}

			return $this->userProvider->getUser($this->login);
		}


		/**
		 *
		 */
		private function revokeUserCookie()
		{
			$this->response->cookies->set('security_user', NULL);
		}
	}
}
