<?php

namespace Officine\Amaka\Operation\Action;

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
        $arguments = func_get_args();
        return call_user_func_array([$this->theInvocable, 'invoke'], $arguments);
    }

    public function getName()
    {
        return $this->theInvocable->getName();
    }

    protected function getInvocable()
    {
        return $this->theInvocable;
    }
}
