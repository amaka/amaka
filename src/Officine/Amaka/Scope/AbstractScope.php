<?php

namespace Officine\Amaka\Scope;

use Officine\Amaka\AmakaScript\DispatchTable;

abstract class AbstractScope
{
    private $dispatchTable;

    public function __construct(DispatchTable $table)
    {
        $this->dispatchTable = $table;
    }

    /**
     * Intercepts all calls
     */
    public function __call($method, $arguments)
    {
        return $this->dispatchTable->handle($method, $arguments);
    }
}
