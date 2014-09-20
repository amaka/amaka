<?php

namespace Officine\Amaka\Action;

use Officine\Amaka\Contrib\Invocable;
use Officine\Amaka\AmakaScript\SymbolTable;

class DependsOnAction extends AbstractAction
{
    private $symbolsTable;

    public function __construct(Invocable $invocable, SymbolTable $symbolsTable)
    {
        $this->symbolsTable = $symbolsTable;
        parent::__construct($invocable);
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
