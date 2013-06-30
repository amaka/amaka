<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginInterface;
use React\Stream\Stream;

class Spawner implements PluginInterface
{
    private $eventLoop;
    private $process;

    public function __construct($eventLoop)
    {
        $this->eventLoop = $eventLoop;
    }

    public function spawn($command)
    {
        $descriptors = array(
            array("pipe", "r"),
            array("pipe", "w"),
            array("pipe", "w"),
        );
        $proc = proc_open(
            $command,
            $descriptors,
            $pipes
        );
        if (false === $proc) {
            throw new \RuntimeException("Could not spawn process '$command'.");
        }

        $writeStream = new Stream($pipes[0], $this->eventLoop);
        $readStream = new Stream($pipes[1], $this->eventLoop);
        $errStream = new Stream($pipes[2], $this->eventLoop);

        $readStream->on('end', function() use ($proc) {
            proc_close($proc);
        });

        return function($callback, $errback = null) use ($readStream, $errStream) {
            if ($errback) {
                $errStream->on('data', $errback);
            }
            $readStream->on('data', $callback);
        };
    }
}
