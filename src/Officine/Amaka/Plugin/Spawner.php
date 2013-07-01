<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginInterface;
use Officine\Amaka\EventLoopAwareInterface;

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

    public function getProcHandle($command, $directory)
    {
        $dspec = array(
            array("pipe", "r"),
            array("pipe", "w"),
            array("pipe", "w"),
        );
        $proc = proc_open(
            $command,
            $dspec,
            $pipes,
            $directory
        );

        if (false === $proc) {
            throw new \RuntimeException("Could not spawn process '$command'.");
        }

        return array($proc, $pipes);
    }

    public function spawn($command, $directory = null)
    {
        list($proc, $pipes) = $this->getProcHandle($command, $directory);

        fclose($pipes[0]);

        $self    = $this;
        $message = "The process '$command' terminated with non-zero exit status.";
        $readStream  = new Stream($pipes[1], $this->eventLoop);
        $errorStream = new Stream($pipes[2], $this->eventLoop);

        return function($callback = null, $errback = null, $progback = null)

            use ($self, $proc, $message, $readStream, $errorStream) {

            is_callable($errback) && $readStream->on('error', $errback);
            is_callable($progback) && $readStream->on('data', $progback);

            $outputBuffer = array();
            $readStream->on('data', function($data) use (&$outputBuffer) {
                array_push($outputBuffer, $data);
            });

            $readStream->on('end', function($stream) use ($proc, $message, $callback, $errback, &$outputBuffer) {
                $exitStatus = proc_close($proc);

                if ($exitStatus == 0) {
                    $output = implode("", $outputBuffer);
                    return is_callable($callback) && $callback($output);
                }

                return is_callable($errback) && $errback($message);
            });
        };
    }
}
