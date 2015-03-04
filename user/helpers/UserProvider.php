<?php

	use Inkwell\Auth;
	use Inkwell\View;
	use Inkwell\Security;
	use Dotink\Flourish\Collection;

	class UserProvider implements Security\UserProviderInterface
	{
		const MSG_USER_EXISTS      = 'A user with that e-mail address already exists.';
		const MSG_PROBLEM_CREATING = 'There was a problem creating your account at this time, please try again later.';


		/**
		 *
		 */
		public function __construct(UserQuery $users, PersonQuery $people)
		{
			$this->users   = $users;
			$this->people  = $people;
		}


		/**
		 *
		 */
		public function createUser($data)
		{
			$person = $this->people->filterByEmail($data['email'])->findOneOrCreate();
			$user   = $this->users->filterByPersonId($person->getId())->findOneOrCreate();

			if ($user->getPersonId()) {
				throw new Exception(self::MSG_USER_EXISTS);
			}

			$this->setPassword($user, $data['password']);

			$person->setName($data['name']);
			$person->setFullName($data['full_name']);
			$person->setUser($user);

			try {
				$person->save();

			} catch (Exception $e) {
				throw new Exception(self::MSG_PROBLEM_CREATING);
			}

			return $user;
		}


		public function getJoinPath()
		{
			return '/join';
		}


		public function getLoginPath()
		{
			return '/login';
		}

		/**
		 * Gets a user from a login
		 */
		public function getUser($login = NULL)
		{
			if (!$login) {
				return new Auth\AnonymousUser();
			}

			return $this->users->create()
				->usePersonQuery()
					->filterByEmail($login)
				->endUse()
			->findOne();
		}


		/**
		 * Gets a login from a user
		 */
		public function getUserLogin($user)
		{
			if ($user instanceof Auth\AnonymousUser) {
				return NULL;
			}

			return $user->getPerson()->getEmail();
		}


		/**
		 *
		 */
		public function getLoginRedirect($user)
		{
			if (isset($_SESSION['USER_PROVIDER_LOGIN_REDIRECT'])) {
				return $_SESSION['USER_PROVIDER_LOGIN_REDIRECT'];
			}

			return '/dashboard';
		}


		/**
		 *
		 */
		public function getLogoutRedirect($user)
		{
			return '/login';
		}


		/**
		 *
		 */
		public function handleJoin(Collection $params, $token, View $view)
		{

		}


		/**
		 *
		 */
		public function handleRegister(Collection $params, array $token_data, View $view)
		{

		}


		/**
		 *
		 */
		public function setLoginRedirect($user, $location)
		{
			$_SESSION['USER_PROVIDER_LOGIN_REDIRECT'] = $location;
		}


		/**
		 *
		 */
		public function setPassword($user, $password)
		{
			return $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
		}


		/**
		 *
		 */
		public function verifyPassword($user, $password)
		{
			if ($user) {
				return password_verify($password, $user->getPassword());
			}

			return FALSE;
		}


		/**
		 *
		 */
		public function verifyUser($user)
		{

		}
	}
