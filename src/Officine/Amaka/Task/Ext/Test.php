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

    public function setTestDirectory($directory)
    {
        $this->testDirectory = $directory;
        return $this;
    }

    public function setConfigurationFile($configuration)
    {
        $this->phpunitConfig = $configuration;
    }

    public function getPHPUnitCommand()
    {
        $testDir = $this->testDirectory;
        $outputFile = $this->outputFile;
        $configFile = $this->phpunitConfig;

        $options = "--stderr --stop-on-error --log-json {$outputFile}";

        return "{$this->phpunitCommand} {$options} -c {$configFile} {$testDir}";
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

        $process = popen($this->getPHPUnitCommand(), 'r');

        // block until PHPUnit has done running
        if ($process) {
            while (! feof($process)) {
                fread($process, 8);
            };
            pclose($process);
        }

        $report = JsonSplitter::split(file_get_contents($this->outputFile));

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
