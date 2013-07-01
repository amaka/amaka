<?php

namespace Officine\Amaka;

use React\EventLoop\LoopInterface;

interface EventLoopAwareInterface
{
    public function setEventLoop(LoopInterface $loop);
}
