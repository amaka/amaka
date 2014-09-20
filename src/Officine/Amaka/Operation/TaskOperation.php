<?php

namespace Officine\Amaka\Operation;

use Officine\Amaka\Contrib\Invocable;
use Officine\Amaka\Task\Task;
use Officine\Amaka\Scope\TaskScope;
use Officine\Amaka\AmakaScript\DispatchTable;
use Officine\Amaka\AmakaScript\SymbolTable;
use Officine\Amaka\ErrorReporting\Trigger;
use Officine\Amaka\Action\DependsOnAction;

class TaskOperation implements OperationInterface
{
    private $taskName;
    private $helpersTable;
    private $symbolsTable;

    public function __construct(DispatchTable $helpersTable, SymbolTable $symbolsTable)
    {
        $this->symbolsTable = $symbolsTable;
        $this->helpersTable = $helpersTable;
    }

    public function invoke($taskName, $codeFragment = null)
    {
        if (is_string($taskName)) {
            $scope = new TaskScope($this->helpersTable);

            if ($codeFragment instanceof \Closure) {
                $codeFragment = $codeFragment->bindTo($scope);
            }

            $this->taskName = $taskName;
            return new DependsOnAction(new Task($taskName, $codeFragment), $this->symbolsTable);
        }

        Trigger::error('Invalid task name')
            ->setLongMessage("Don't know what to do with '$taskName'.")
            ->trigger();
    }

    public function getName()
    {
        return $this->taskName;
    }

    public function dependsOn()
    {
        if (func_num_args()) {
            $requiredTasks = func_get_args();
            $this->symbolsTable->addSymbol($this->getName(), $requiredTasks);
        }
        return $this;
    }
}
