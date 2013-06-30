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

use Officine\Amaka\Task\AbstractTask;
use Officine\Amaka\FailedBuildException;

use Officine\Amaka\PluginBroker;
use Officine\Amaka\Plugin\PluginAwareInterface;

class Test extends AbstractTask implements PluginAwareInterface
{
    const IGNORE_OPTION = -1;

    private $pluginBroker = null;
    private $watchedDirectory = null;

    private $phpunitCommand = null;
    private $failOnError    = true;
    private $testDirectory  = self::IGNORE_OPTION;
    private $phpunitConfig  = self::IGNORE_OPTION;

    public function __construct($name = 'Test')
    {
        $resolver = new FileResolver();
        $resolver->addPath(realpath(__DIR__ . '/../../../../../') . '/vendor/bin')
                 ->addPath('/usr/local/bin')
                 ->addPath('/usr/bin');

        $test = $this;
        $resolver->resolve('phpunit', function($command) use ($test) {
            $test->setPHPUnitCommand($command);
        }, function() {
            throw new \RuntimeException("Amaka could not locate a PHPUnit binary, try to declare one with setPHPUnitCommand(\$bin).");
        });

        parent::__construct($name);
    }

    public function setTestDirectory($directory)
    {
        $this->testDirectory = $directory;
        return $this;
    }

    public function getTestDirectory()
    {
        return $this->testDirectory;
    }

    public function setConfig($config)
    {
        $this->phpunitConfig = $config;
        return $this;
    }

    public function setNoTestDirectory()
    {
        $this->testDirectory = self::IGNORE_OPTION;
        return $this;
    }

    public function setNoConfig()
    {
        $this->phpunitConfig = self::IGNORE_OPTION;
        return $this;
    }

    public function setPHPUnitCommand($command)
    {
        $this->phpunitCommand = $command;
        return $this;
    }

    public function plugin($plugin)
    {
        return $this->getPluginBroker()
                    ->getPlugin($plugin);
    }

    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }

    public function setPluginBroker(PluginBroker $broker)
    {
        $this->pluginBroker = $broker;
        return $this;
    }

    public function setFailOnError($fail = true)
    {
        if (! $fail) {
            $this->failOnError = self::IGNORE_OPTION;
            return $this;
        }
        $this->failOnError = true;
        return $this;
    }

    public function getPHPUnitCommand($withOptions = true)
    {
        if (false === $withOptions) {
            return $this->phpunitCommand;
        }

        $options    = ($this->failOnError == self::IGNORE_OPTION ? "" : "--stop-on-error");
        $useConfig  = ($this->phpunitConfig == self::IGNORE_OPTION ? "" : "-c {$this->phpunitConfig}");

        return sprintf("{$this->phpunitCommand} %s %s",
                       $options,
                       $useConfig
        );
    }

    /**
     *
     */
    public function invoke()
    {
        if (self::IGNORE_OPTION != $this->testDirectory
            && ! file_exists($this->testDirectory)) {
            throw new \RuntimeException("No test directory specified, or directory not found.");
        }

        if (self::IGNORE_OPTION != $this->phpunitConfig
            && ! file_exists($this->phpunitConfig)) {
            throw new \RuntimeException("Could not load the specified PHPUnit configuration '{$this->phpunitConfig}'");
        }

        echo "Using PHPUnit ({$this->getPHPUnitCommand(false)})\n";

        $process = $this->plugin('spawner')
                        ->spawn($this->getPHPUnitCommand(), realpath($this->getTestDirectory()));

        $failOnError = $this->failOnError;
        $process(null, function($error) use ($failOnError) {
            if (self::IGNORE_OPTION == $failOnError) {
                return;
            }
            throw new FailedBuildException($error);
        }, function($progress) {
            echo $progress;
        });
    }
}
