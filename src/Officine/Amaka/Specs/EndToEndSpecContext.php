<?php

namespace Officine\Amaka\Specs;

class EndToEndSpecContext extends SpecContext
{
    private $amakaCommand;
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
        system($this->getAmakaCommand(), $exitCode);

        return 0 === (int) $exitCode;
    }
}
