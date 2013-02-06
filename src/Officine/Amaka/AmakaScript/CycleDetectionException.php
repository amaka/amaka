<?php

namespace Officine\Amaka\AmakaScript;

class CycleDetectionException extends BuildfileException
{
    public function __construct($cycles, $code = 0, Exception $previous = null)
    {
        $cycles  = implode(', ', end($cycles));
        parent::__construct("Cycle detected in amaka script ({$cycles}).");
    }
}
