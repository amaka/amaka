<?php

namespace Officine\Amaka\Operation;

use Officine\Amaka\Invocable;
use Officine\Amaka\AmakaScript\ExpressionBuilder;
use Officine\Amaka\AmakaScript\SymbolTable;

class AbstractOperation implements OperationInterface
{
    private $symbolTable;

    public function __construct(SymbolTable $table)
    {
        $this->symbolTable = $table;
    }

    public function getInvocableExpressionBuilder(Invocable $invocable)
    {
        return new ExpressionBuilder($this->symbolTable, $invocable);
    }
}
