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

use React\Stream\Stream;
use React\EventLoop\Factory as EventLoopFactory;

class Test extends Task
{
    const IGNORE_OPTION = -1;

    private $phpunitCommand   = null;
    private $testDirectory    = self::IGNORE_OPTION;
    private $phpunitConfig    = self::IGNORE_OPTION;

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

    public function getPHPUnitCommand($withOptions = true)
    {
        if (false === $withOptions) {
            return $this->phpunitCommand;
        }

        $options    = "--stop-on-error";
        $useConfig  = ($this->phpunitConfig == self::IGNORE_OPTION ? "" : "-c {$this->testDirectory}");

        return sprintf("{$this->phpunitCommand} %s %s",
                       $useConfig,
                       $options
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

        $dspec = array(
            array("pipe", "r"),
            array("pipe", "w"),
            array("pipe", "w") // stderr is a file to write to
        );

        $proc = proc_open(
            $this->getPHPUnitCommand(),
            $dspec,
            $pipes,
            $this->getTestDirectory());

        if (false === $proc) {
            throw new \RuntimeException("Could not run PHPUnit.");
        }

        // Let's discard stdin to the child process
        fclose($pipes[0]);

        $loop = EventLoopFactory::create();
        $readStream = new Stream($pipes[1], $loop);
        $readStream->on('data', function($data) {
            if (! $data) {
                return;
            }
            echo $data;
        });
        $errorStream = new Stream($pipes[2], $loop);
        $errorStream->on('data', function($error) {
            if (! $error) {
                return;
            }
            echo "error: {$error}\n";
        });

        $readStream->once('end', function() use ($proc) {
            $exitStatus = proc_close($proc);
            if ($exitStatus > 0) {
                throw new FailedBuildException("There were some failed tests");
            }
        });
        $loop->run();
    }
}
