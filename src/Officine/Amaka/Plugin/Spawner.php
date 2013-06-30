<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginInterface;
use React\Stream\Stream;

class Spawner implements PluginInterface
{
    private $process;
    private $eventLoop;

    public function __construct($eventLoop)
    {
        $this->eventLoop = $eventLoop;
    }

    public function spawn($command, $directory = null)
    {
        $descriptors = array(
            array("pipe", "r"),
            array("pipe", "w"),
            array("pipe", "w"),
        );
        $proc = proc_open(
            $command,
            $descriptors,
            $pipes,
            $directory
        );

        if (false === $proc) {
            throw new \RuntimeException("Could not spawn process '$command'.");
        }

        fclose($pipes[0]);

        $readStream  = new Stream($pipes[1], $this->eventLoop);
        $errorStream = new Stream($pipes[2], $this->eventLoop);

        $self = $this;
        return function($callback = null, $errback = null, $progback = null)
            use ($self, $proc, $command, $readStream, $errorStream) {
            $readStream->on('data', function($data) use ($progback) {
                is_callable($progback) && $progback($data);
            });

            $readStream->on('error', function($error) use ($errback) {
                is_callable($errback) && $errback($error);
            });

            $readStream->on('end', function($stream) use ($proc, $command, $callback, $errback) {
                $exitStatus = proc_close($proc);
                if ($exitStatus > 0) {
                    return is_callable($errback) && $errback("The process '$command' terminated with non-zero exit status.");
                }
                is_callable($callback) && $callback($exitStatus);
            });
        };
    }
}
