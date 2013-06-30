<?php

namespace Officine\Amaka\Task;

use Officine\Amaka\Invocable;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\Plugin\PluginAwareInterface;

abstract class AbstractTask implements Invocable, PluginAwareInterface
{
    /**
     * Name of the task
     *
     * @var string
     */
    private $name;

    /**
     *
     */
    private $pluginBroker;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Return the name of the task
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function plugin($plugin)
    {
        return $this->getPluginBroker()->plugin($plugin);
    }

    /**
     *
     */
    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }

    /**
     *
     */
    public function setPluginBroker(PluginBroker $broker)
    {
        $this->pluginBroker = $broker;
        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
