<?php

use iMarc\Auth;
use Inkwell\Auth\ConsumerInterface;
use Base\TaskQuery as BaseTaskQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'tasks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class TaskQuery extends BaseTaskQuery implements ConsumerInterface
{
	/**
	 *
	 */
	private $auth = NULL;


	/**
	 *
	 */
	public function setAuthManager(Auth\Manager $manager)
	{
		$this->auth = $manager;
	}


	/**
	 *
	 */
	public function buildInDateRange($start_date, $end_date, $active_date)
	{
		$data = array();

		$leftover_tasks = $this->create()
			->where('Task.StartDate  < ?', $active_date)
			->where('Task.AssigneeId = ?', $this->auth->entity->getPersonId())
			->where('Task.TimeCompleted IS NULL')
			->find();

		foreach ($leftover_tasks as $i => $task) {
			$task->setStartDate($active_date);
			$task->setPriority($i + 1);
			$task->save();
		}

		$historic_tasks = $this->create()
			->where('Task.StartDate  < ?', $active_date)
			->where('Task.AssigneeId = ?', $this->auth->entity->getPersonId())
			->where('Task.TimeCompleted IS NOT NULL')
			->orderBy('priority')
			->find();

		$emerging_tasks = $this->create()
			->where('Task.StartDate >= ?', $active_date)
			->where('Task.AssigneeId = ?', $this->auth->entity->getPersonId())
			->orderBy('priority')
			->find();

		foreach ([$historic_tasks, $emerging_tasks] as $tasks) {
			foreach ($tasks as $task) {
				$start_date = $task->getStartDate()->format('U');

				if (!isset($data[$start_date])) {
					$data[$start_date] = array();
				}

				$data[$start_date][] = $task;
			}
		}

		return $data;
	}
}
