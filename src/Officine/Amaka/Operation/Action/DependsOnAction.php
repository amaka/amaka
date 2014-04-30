<?php

namespace Officine\Amaka\Operation\Action;

use Officine\Amaka\Invocable;
use Officine\Amaka\AmakaScript\SymbolTable;

class DependsOnAction extends AbstractAction
{
    private $symbolTable;

    public function __construct(Invocable $invocable, SymbolTable $symbolTable)
    {
        $this->symbolTable = $symbolTable;
        parent::__construct($invocable);
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
