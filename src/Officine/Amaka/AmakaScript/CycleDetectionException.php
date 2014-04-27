<?php

namespace Officine\Amaka\AmakaScript;

/**
 * @codeCoverageIgnore
 */
class CycleDetectionException extends BuildfileException
{
    public function __construct($cycles)
    {
        $cycles  = implode(', ', end($cycles));
        parent::__construct("Cycles detected: {$cycles}.");
    }
}
