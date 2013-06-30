<?php

namespace Officine\Amaka\Task\Ext;

use Officine\Amaka\Task\AbstractTask;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\Plugin\PluginAwareInterface;

class Watch extends AbstractTask implements PluginAwareInterface
{
    private $pluginBroker = null;
    private $watchedDirectory = null;

    public function __construct($name = null)
    {
        if (null === $name) {
            $name = "Watch";
        }
        parent::__construct($name);
    }

    public function setWatchedDirectory($directory)
    {
        $this->watchedDirectory = $directory;
        return $this;
    }

    public function getWatchedDirectory()
    {
        return $this->watchedDirectory;
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

    public function invoke()
    {
        if (! $this->getWatchedDirectory()) {
            throw new \InvalidArgumentException("Please provide a directory to watch for changes.");
        }

        $directory = $this->getWatchedDirectory();

        $test = new Test();
        $test->setTestDirectory($directory);
        $test->setPluginBroker($this->getPluginBroker());
        $test->invoke();

        $events = implode(',', array(
            'modify',
            'attrib',
            'move',
            'close_write',
            'create',
            'delete'
        ));

        $process = $this->plugin('spawner')
                        ->spawn("inotifywait -qrme {$events} {$directory}");

        $process(null, function($error) {
            if (! $error) {
                return;
            }
            echo "inotify error: {$error}\n";
        }, function($progress) use ($test) {
            if (strpos($progress, 'CLOSE_WRITE')) {
                $test->invoke();
            }
        });
    }
}