<?php

namespace Officine\Amaka\Operation\UnitTest;

use Officine\StdLib\CommandBuilder;
use Symfony\Component\Process\Process;

class PHPUnitDriver implements TestDriverInterface
{
    private $testDirectory;
    private $configFilePath;
    private $bootstrapFilePath;
    private $phpUnitExecutable = "vendor/bin/phpunit";

    public function usePHPUnitExecutable($exutablePath)
    {
        $this->phpUnitExecutable = $exutablePath;
        return $this;
    }

    public function testDirectory($testDirectory)
    {
        $this->testDirectory = $testDirectory;
        return $this;
    }

    public function useConfigFile($configFilePath)
    {
        $this->configFilePath = $configFilePath;
        return $this;
    }

    public function useBootstrapFile($bootstrapFilePath)
    {
        $this->bootstrapFilePath = $bootstrapFilePath;
        return $this;
    }

    public function run()
    {
        $builder = new CommandBuilder($this->phpUnitExecutable);

        if ($this->configFilePath) {
            $builder->addArgument('-c', $this->configFilePath);
        }

        if ($this->bootstrapFilePath) {
            $builder->addArgument('--bootstrap', $this->bootstrapFilePath);
        }

        if ($this->testDirectory) {
            $builder->addArgument($this->testDirectory);
        }

        $process = new Process($builder->getCommandString());
        $process->start();
        while ($process->isRunning()) {
            echo $process->getIncrementalOutput();
        }
    }
}
