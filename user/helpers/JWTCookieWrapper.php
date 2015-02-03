<?php namespace Inkwell\Security
{
	use UnexpectedValueException;
	use Inkwell\HTTP;
	use JWT;

	class JWTCookieWrapper implements HTTP\CookieWrapperInterface
	{
		/**
		 *
		 */
		private $key = NULL;


		/**
		 *
		 */
		public function __construct($key)
		{
			$this->key = $key;
		}


		/**
		 *
		 */
		public function wrap($data)
		{
			return JWT::encode($data, $this->key);
		}


		/**
		 *
		 */
		public function unwrap($data)
		{
			try {
				return JWT::decode($data, $this->key);
			} catch (UnexpectedValueException $e) {
				return NULL;
			}
		}
	}
}
