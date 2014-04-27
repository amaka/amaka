<?php

namespace Officine\Amaka\Operation;

use Officine\Amaka\Task\Task;
use Officine\Amaka\ErrorReporting\Trigger;

class TaskOperation extends AbstractOperation
{
    public function invoke($taskName, $codeFragment = null)
    {
        if (is_string($taskName)) {
            $task = new Task($taskName, $codeFragment);
            return $this->getInvocableExpressionBuilder($task);
        }
        Trigger::error('Invalid task name')
            ->setLongMessage("Don't know what to do with '$taskName'.")
            ->trigger();
    }
}
