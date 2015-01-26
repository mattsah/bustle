<?php

	use IW\HTTP;
	use Inkwell\Controller;
	use Dotink\Flourish;

	/**
	 *
	 */
	class MainController extends Controller\BaseController
	{
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
		}


		/**
		 *
		 */
		public function home()
		{
			$date = $this->date->adjust('-1 day');

			for ($days = array(); count($days) != 5; $date = $date->adjust('+1 day')) {
				$days[] = $date;
			}

			return $this['view']
				->load('main/home.html')
				->assign('inner', 'main/home.html')
				->set([
					'days' => $days
				]);
		}
	}
