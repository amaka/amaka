<?php

namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\Invocable;

class ExpressionBuilder implements Invocable
{
    private $theInvocable;
    private $symbolTable;

    public function __construct(SymbolTable $symbolTable, Invocable $invocable)
    {
        $this->symbolTable = $symbolTable;
        $this->theInvocable = $invocable;
    }

    public function invoke()
    {
        $arguments = func_get_args();
        return call_user_func_array([$this->theInvocable, 'invoke'], $arguments);
    }

    public function getName()
    {
        return $this->theInvocable->getName();
    }

    public function dependsOn()
    {
        if (func_num_args()) {
            $requiredTasks = func_get_args();
            $this->symbolTable->addSymbol($this->getName(), $requiredTasks);
        }
        return $this;
    }
}
