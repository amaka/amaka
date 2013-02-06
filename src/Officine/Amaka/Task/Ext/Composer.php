<?php

namespace Officine\Amaka\Task\Ext;

use Officine\Amaka\Task\Task;

class Composer extends Task
{
    public function invoke()
    {
        parent::invoke();

        $future = new \ExecFuture("composer --no-ansi update");
        $future->start();

        try {
            do {
                list($out) = $future->read();
                echo $out;
            } while (! $future->isReady());
        } catch (\Exception $e) {
            list($err) = $future->read();
            throw new \Exception($err);
        }
    }
}
