<?php

namespace Officine\Amaka\AmakaScript;

/**
 * @codeCoverageIgnore
 */
class CycleDetectionException extends BuildfileException
{
    public function __construct($cycles, $code = 0, Exception $previous = null)
    {
        $cycles  = implode(', ', end($cycles));
        parent::__construct("Cycles detected: {$cycles}.");
    }
}
