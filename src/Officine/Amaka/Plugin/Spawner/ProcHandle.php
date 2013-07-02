<?php

namespace Officine\Amaka\Plugin\Spawner;

use React\Stream\Stream;
use React\EventLoop\LoopInterface;

class ProcHandle
{
    private $command;
    private $directory;
    private $eventLoop;

    private $readStream;
    private $writeStream;
    private $errorStream;

    public function __construct($command, $directory, $eventLoop)
    {
        $this->command = $command;
        $this->directory = $directory;
        $this->eventLoop = $eventLoop;
    }

    public function setReadStream(Stream $stream)
    {
        $this->readStream = $stream;
        return $this;
    }

    public function getReadStream()
    {
        return $this->readStream;
    }

    public function setWriteStream(Stream $stream)
    {
        $this->writeStream = $stream;
        return $stream;
    }

    public function getWriteStream()
    {
        return $this->writeStream;
    }

    public function setErrorStream(Stream $stream)
    {
        $this->errorStream = $stream;
        return $this;
    }

    public function getErrorStream()
    {
        return $this->errorStream;
    }

    public function createProcess($command, $directory)
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

    public function start()
    {
        list($process, $pipes) = $this->createProcess(
            $this->command,
            $this->directory
        );

        $this->process = $process;

        $this->setWriteStream(new Stream($pipes[0], $this->eventLoop));
        $this->setReadStream(new Stream($pipes[1], $this->eventLoop));
        $this->setErrorStream(new Stream($pipes[2], $this->eventLoop));
    }

    public function close()
    {
        $this->readStream->close();
        $this->writeStream->close();
        $this->errorStream->close();

        return proc_close($this->process);
    }

    public function kill()
    {
        $this->readStream->close();
        $this->writeStream->close();
        $this->errorStream->close();

        return proc_terminate($this->process);
    }
}
