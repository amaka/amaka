<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task\Ext;

use Officine\StdLib\FileResolver;
use Officine\StdLib\JsonSplitter;

use Officine\Amaka\Context;
use Officine\Amaka\Task\Task;
use Officine\Amaka\FailedBuildException;

class Test extends Task
{
    private $testDirectory = 'tests';
    private $phpunitConfig = 'phpunit.xml';
    private $phpunitCommand;

    public function __construct($name)
    {
        $resolver = new FileResolver();
        $resolver->addPath(realpath(__DIR__ . '/../../../../../') . '/vendor/bin')
                 ->addPath('/usr/local/bin')
                 ->addPath('/usr/bin');

        $test = $this;
        $resolver->resolve('phpunit', function($command) use ($test) {
            echo "PHPUnit command '$command'\n";
            $test->setPHPUnitCommand($command);
        }, function() {
            throw new \RuntimeException("PHPUnit doesn't seem to be installed anywhere on this system.");
        });

        parent::__construct($name);
    }

    public function setPHPUnitCommand($command)
    {
        $this->phpunitCommand = $command;
        return $this;
    }

    public function setTestDirectory($directory)
    {
        $this->testDirectory = $directory;
        return $this;
    }

    public function setNoTestDirectory()
    {
        $this->testDirectory = -1;
        return $this;
    }

    public function setNoConfig()
    {
        $this->phpunitConfig = -1;
        return $this;
    }

    public function setConfigurationFile($configuration)
    {
        $this->phpunitConfig = $configuration;
        return $this;
    }

    public function getPHPUnitCommand()
    {
        $useConfig = $this->phpunitConfig;
        $useTestDir = $this->testDirectory;

        $options = "--stderr --stop-on-error";

        $useConfig = ($useConfig == -1 ? "" : "-c {$useConfig}");
        $useTestDir = ($useTestDir == -1 ? "" : "{$useTestDir}");

        return sprintf("{$this->phpunitCommand} %s %s %s",
                       $useConfig,
                       $options,
                       $useTestDir
        );
    }

    /**
     * TODO: Replace the code required to spawn the PHPUnit executable
     * with PHPUnit objects.
     *
     */
    public function invoke()
    {
        parent::invoke();

        if ($this->testDirectory != -1
            && ! file_exists($this->testDirectory)) {
            throw new \RuntimeException("No test directory specified, or directory not found.");
        }

        if ($this->phpunitConfig != -1
            && ! file_exists($this->phpunitConfig)) {
            throw new \RuntimeException("Could not load the specified PHPUnit configuration '{$this->phpunitConfig}'");
        }

        if ($this->phpunitConfig == -1
            && $this->testDirectory == -1) {
            return;
        }

        $exitStatus = false;
        system($this->getPHPUnitCommand());

        if (! false === $exitStatus) {
            throw new FailedBuildException("Could not start the test framework.");
        }

        if ($exitStatus > 0) {
            throw new FailedBuildException();
        }
    }
}
