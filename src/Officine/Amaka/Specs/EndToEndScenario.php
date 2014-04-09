<?php

namespace Officine\Amaka\Specs;

use Behat\Behat\Context\BehatContext;

class EndToEndScenario extends BehatContext
{
    private $arguments = [];
    private $amakaCommand;
    private $contextsDirectory;

    public function __construct(array $parameters)
    {
        $contextFileSuffix = 'Context.php';

        $this->setAmakaCommand($parameters['amaka_command']);
        $this->setContextDirectory($parameters['context_search_directory']);

        if (isset($parameters['load_context_classes'], $parameters['context_search_directory'])
        && $parameters['load_context_classes'] && is_dir($parameters['context_search_directory'])) {
            $it = new \GlobIterator($parameters['context_search_directory'] . '/*.php');
            foreach ($it as $file) {
                echo $file . PHP_EOL;
            }
        }
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

    public function setContextDirectory($contextsDirectory)
    {
        $this->contextsDirectory = $contextsDirectory;
        return $this;
    }

    public function getContextDirectory()
    {
        return $this->contextsDirectory;
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
