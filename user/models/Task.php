<?php

use Base\Task as BaseTask;

/**
 * Skeleton subclass for representing a row from the 'tasks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Task extends BaseTask implements API\ResourceInterface
{
    public function post($values, $auth)
    {
        $this->setTitle($values['title']);
        $this->setUserRelatedByOwnerId($auth->entity);

        if (isset($values['assignee'])) {
            $this->setUserRelatedByAssigneeId($auth->entity);

            if (isset($values['startDate'])) {
                $this->setStartDate($values['startDate']);
            }
        }
    }
}
