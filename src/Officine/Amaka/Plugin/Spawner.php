<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginInterface;
use Officine\Amaka\EventLoopAwareInterface;
use Officine\Amaka\Plugin\Spawner\ProcHandle;

use React\Stream\Stream;
use React\EventLoop\LoopInterface;

class Spawner implements PluginInterface, EventLoopAwareInterface
{
    private $process;
    private $eventLoop;

    public function __construct($eventLoop)
    {
        $this->setEventLoop($eventLoop);
    }

    public function setEventLoop(LoopInterface $loop)
    {
        $this->eventLoop = $loop;
        return $this;
    }

    public function spawnManaged($command, $directory = null)
    {
        $eventLoop = $this->eventLoop;
        $handle = new ProcHandle($command, $directory, $eventLoop);

        return function ($callback = null) use ($handle, $eventLoop) {
            is_callable($callback) && $callback($handle);
            return $handle;
        };
    }

    public function spawn($command, $directory = null)
    {
        $eventLoop = $this->eventLoop;
        $handle = new ProcHandle($command, $directory, $eventLoop);

        $self    = $this;
        $message = "The process '$command' terminated with non-zero exit status.";

        $handle->start();

        $readStream  = $handle->getReadStream();
        $errorStream = $handle->getErrorStream();

        return function($callback = null, $errback = null, $progback = null)

            use ($self, $handle, $message, $readStream, $errorStream) {

            is_callable($errback) && $readStream->on('error', $errback);
            is_callable($progback) && $readStream->on('data', $progback);

            $outputBuffer = array();
            $readStream->on('data', function($data) use (&$outputBuffer) {
                array_push($outputBuffer, $data);
            });

            $readStream->on('end', function($stream) use ($handle, $message, $callback, $errback, &$outputBuffer) {
                $exitStatus = $handle->close();

                if ($exitStatus == 0) {
                    $output = implode("", $outputBuffer);
                    return is_callable($callback) && $callback($output);
                }

                return is_callable($errback) && $errback($message);
            });
        };
    }
}
