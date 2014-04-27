<?php

namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\Task\Task;

class TaskOperation implements OperationInterface
{
    private $symbolTable;

    public function __construct(SymbolTable $table)
    {
        $this->symbolTable = $table;
    }

    public function invoke($taskName, $codeFragment = null)
    {
        if (is_string($taskName)) {
            $task = new Task($taskName, $codeFragment);
            return new ExpressionBuilder($this->symbolTable, $task);
        }
        throw new \Exception("Don't know what to do with this task name");
    }
}
