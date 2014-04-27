<?php

namespace Officine\Amaka\AmakaScript;

class ScriptScope
{
    private $symbolTable;

    public function __construct($table)
    {
        $this->symbolTable = $table;
    }

    /**
     * Intercepts all calls
     */
    public function __call($method, $arguments)
    {
        return $this->symbolTable->handle($method, $arguments);
    }
}
