<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task\Ext;

use Officine\StdLib\JsonSplitter;
use Officine\Amaka\Context;
use Officine\Amaka\Task\Task;
use Officine\Amaka\FailedBuildException;

class Test extends Task
{
    private $outputFile;
    private $testDirectory = 'tests';
    private $phpunitConfig = 'phpunit.xml';
    private $phpunitCommand = 'vendor/bin/phpunit';

    public function __construct($name)
    {
        $this->phpunitCommand = ROOT_DIR . '/vendor/bin/phpunit';
        parent::__construct($name);
    }

    public function setTestDirectory($directory)
    {
        $this->testDirectory = $directory;
        return $this;
    }

    public function setNoConfigurationFile()
    {
        $this->phpunitConfig = false;
        return $this;
    }

    public function setConfigurationFile($configuration)
    {
        $this->phpunitConfig = $configuration;
        return $this;
    }

    public function getPHPUnitCommand()
    {
        $testDir = $this->testDirectory;
        $outputFile = $this->outputFile;
        $configFile = $this->phpunitConfig;

        $options = "--stderr --stop-on-error --log-json {$outputFile}";

        $useConfig = $configFile ? "-c {$configFile}" : "";

        return "{$this->phpunitCommand} {$options} {$useConfig} {$testDir}";
    }

    /**
     * TODO: Replace the code required to spawn the PHPUnit executable
     * with PHPUnit objects.
     *
     */
    public function invoke()
    {
        parent::invoke();

        $this->outputFile = tempnam(null, 'amk.test.json');

        if (! file_exists($this->testDirectory)) {
            throw new \RuntimeException("No test directory specified, or directory not found.");
        }

        if (false !== $this->phpunitConfig && ! file_exists($this->phpunitConfig)) {
            throw new \RuntimeException("Could not load the specified PHPUnit configuration '{$this->phpunitConfig}'");
        }

        $process = popen($this->getPHPUnitCommand(), 'r');

        // block until PHPUnit has done running
        if ($process) {
            while (! feof($process)) {
                fread($process, 8);
            };
            pclose($process);
        }

        $report = JsonSplitter::split(file_get_contents($this->outputFile));

        if (! $report[0]) {
            throw new FailedBuildException("Could not start the test framework.");
        }

        foreach ($report as $event) {
            if ('test' != $event->event) {
                continue;
            }
            if ('fail' == $event->status || 'error' == $event->status) {
                $message = "\n{$event->status}\n{$event->message}\n";
                throw new FailedBuildException($message);
            }
        }

        unlink($this->outputFile);
    }
}
