<?php

	use IW\HTTP;
	use Inkwell\Controller;
	use Inkwell\Auth;
	use Dotink\Flourish;

	/**
	 *
	 */
	class MainController extends Controller\BaseController implements Auth\ConsumerInterface
	{
		use Auth\Controller;

		/**
		 *
		 */
		private $collection = NULL;


		/**
		 *
		 */
		private $entity = NULL;


		/**
		 *
		 */
		public function __construct(Inkwell\View $view, Flourish\Date $date)
		{
			$this->view = $view;
			$this->date = $date;
		}


		/**
		 *
		 */
		public function __prepare($action, $context = array())
		{
			parent::__prepare($action, $context);

			$this->view->load('main/' . $action . '.html');
		}


		/**
		 *
		 */
		public function home()
		{

			return $this->view;
		}


		/**
		 *
		 */
		public function dashboard()
		{
			$this->requireAuth('read', 'tasks');

			$date = $this->date->adjust('-1 day');

			for ($days = array(); count($days) != 5; $date = $date->adjust('+1 day')) {
				$days[] = $date;
			}

			$this->view->set([
				'days' => $days
			]);

			return $this->view;
		}
	}
