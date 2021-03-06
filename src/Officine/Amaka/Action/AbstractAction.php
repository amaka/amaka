<?php

namespace Officine\Amaka\Action;

use Officine\StdLib\DynamicCall;
use Officine\Amaka\Contrib\Invocable;

abstract class AbstractAction implements Invocable
{
    private $invocable;

    public function __construct(Invocable $invocable)
    {
        $this->invocable = $invocable;
    }

    public function invoke()
    {
        $call = new DynamicCall('invoke', $this->invocable);
        return $call->withArguments(func_get_args());
    }

    public function getName()
    {
        return $this->invocable->getName();
    }
}
