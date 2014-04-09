<?php

namespace Officine\Amaka\Specs;

use Behat\Behat\Context\BehatContext;

class EndToEndScenario  extends BehatContext
{
    private $arguments = [];
    private $amakaCommand;

    public function __construct($amakaCommandPath)
    {
        $this->setAmakaCommand($amakaCommandPath);
    }

    public function addArgument($argument)
    {
        $this->arguments[$argument] = $argument;
    }

    public function removeArgument($argument)
    {
        if (isset($this->arguments[$argument])) {
            unset($this->arguments[$argument]);
        }
    }

    public function setAmakaCommand($pathToAmaka)
    {
        $this->amakaCommand = $pathToAmaka;
        return $this;
    }

    public function getAmakaCommand()
    {
        if (empty($this->arguments)) {
            return $this->amakaCommand;
        }
        return $this->amakaCommand . ' ' . implode(' ', $this->arguments);
    }

    public function runCommand()
    {
        system($this->getAmakaCommand(), $exitCode);

        return 0 === (int) $exitCode;
    }
}
