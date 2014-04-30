<?php

namespace Officine\Amaka\Operation;

use Officine\Amaka\Invocable;
use Officine\Amaka\Task\Task;
use Officine\Amaka\Scope\TaskScope;
use Officine\Amaka\AmakaScript\DispatchTable;
use Officine\Amaka\AmakaScript\SymbolTable;
use Officine\Amaka\ErrorReporting\Trigger;
use Officine\Amaka\Operation\Action\DependsOnAction;

class TaskOperation implements OperationInterface
{
    private $symbolsTable;
    private $dispatchTable;

    public function __construct(SymbolTable $symbolsTable, DispatchTable $dispatchTable)
    {
        $this->symbolsTable = $symbolsTable;
        $this->dispatchTable = $dispatchTable;
    }

    public function invoke($taskName, $codeFragment = null)
    {
        if (is_string($taskName)) {
            $scope = new TaskScope($this->dispatchTable);

            if ($codeFragment instanceof \Closure) {
                $codeFragment = $codeFragment->bindTo($scope);
            }

            $task = new Task($taskName, $codeFragment);
            return new DependsOnAction($task, $this->symbolsTable);
        }
        Trigger::error('Invalid task name')
            ->setLongMessage("Don't know what to do with '$taskName'.")
            ->trigger();
    }
}
