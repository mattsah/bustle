<?php

	use IW\HTTP;
	use Inkwell;
	use Dotink\Flourish;
	use Propel\Runtime\Map\TableMap;

	/**
	 *
	 */
	class MainController extends Inkwell\Controller
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
		public function __construct(Inkwell\View $view)
		{
			$this['view'] = $view;
		}


		/**
		 *
		 */
		public function prepare($action, $context = array())
		{
			parent::prepare($action, $context);
		}


		/**
		 *
		 */
		public function home()
		{
			$this['response']->setStatus(HTTP\OK);
			$this['response']->set(
				$this['view']
					->load('main/home.html')
					->assign('inner', 'main/home.html')
					->compose()
			);

			return $this['response'];
		}
	}