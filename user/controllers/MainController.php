<?php

	use IW\HTTP;

	use Inkwell\Controller;
	use Inkwell\View;
	use Inkwell\Auth;

	use Dotink\Flourish;
	use Dotink\Flourish\Date;

	/**
	 *
	 */
	class MainController extends Controller\BaseController implements Auth\ConsumerInterface
	{
		use Auth\ControllerConsumer;

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
		public function __construct(View $view, Date $date, TaskQuery $tasks)
		{
			$this->view  = $view;
			$this->date  = $date;
			$this->tasks = $tasks;
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
			$this->requireAuth('read', 'task');

			$date = $this->date->adjust('-1 day');

			for ($days = array(); count($days) != 5; $date = $date->adjust('+1 day')) {
				$days[] = $date;
			}

			$tasks = $this->tasks->buildInDateRange(
				reset($days)->format('Y-m-d'),
				end($days)->format('Y-m-d'),
				$this->date->format('Y-m-d')
			);

			$this->view->set([
				'days'  => $days,
				'tasks' => $tasks
			]);

			return $this->view;
		}
	}
