<?php

namespace Officine\Amaka\Action;

use Officine\Amaka\Invocable;

abstract class AbstractAction implements Invocable
{
    private $theInvocable;

    public function __construct(Invocable $invocable)
    {
        $this->theInvocable = $invocable;
    }

    public function invoke()
    {
        return call_user_func_array([$this->theInvocable, 'invoke'], func_get_args());
    }

    public function getName()
    {
        return $this->theInvocable->getName();
    }
}
