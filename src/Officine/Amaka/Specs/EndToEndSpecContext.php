<?php

namespace Officine\Amaka\Specs;

class EndToEndSpecContext extends SpecContext
{
    private $amakaCommand;
    private $lastExitStatus;
    private $workingDirectory;
    private $collectedOutput = [];
    private $commandArguments = [];

    const AMAKA_CMD_KEY = 'amaka_command';

    public function __construct(array $parameters = [])
    {
        !isset($parameters[self::AMAKA_CMD_KEY]) ?: $this->setAmakaCommand($parameters[self::AMAKA_CMD_KEY]);
        parent::__construct($parameters);
    }

    public function addArgument($argument)
    {
        $this->commandArguments[$argument] = $argument;
    }

    public function removeArgument($argument)
    {
        if (isset($this->commandArguments[$argument])) {
            unset($this->commandArguments[$argument]);
        }
    }

    public function setWorkingDirectory($directory)
    {
        $this->workingDirectory = $directory;
        return $this;
    }

    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    public function setAmakaCommand($pathToAmaka)
    {
        $this->amakaCommand = $pathToAmaka;
        return $this;
    }

    public function getAmakaCommand()
    {
        if (empty($this->commandArguments)) {
            return $this->amakaCommand;
        }
        return $this->amakaCommand . ' ' . implode(' ', $this->commandArguments);
    }

    public function runCommand()
    {
        $descriptors = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
        $process = proc_open($this->getAmakaCommand(), $descriptors, $pipes, $this->workingDirectory);

        if (is_resource($process)) {
            $this->collectedOutput[] = stream_get_contents($pipes[1]);
            $this->collectedOutput[] = stream_get_contents($pipes[2]);

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            $this->lastExitStatus = (int) proc_close($process);
        }
        return 0 === $this->lastExitStatus;
    }

    public function getLastExitStatus()
    {
        return $this->lastExitStatus;
    }

    public function getCollectedOutput()
    {
        return implode(PHP_EOL, $this->collectedOutput);
    }
}
