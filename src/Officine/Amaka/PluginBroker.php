<?php

namespace Officine\Amaka;

class PluginBroker
{
    private $plugins = array();

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function registerPlugins(array $plugins)
    {
        $broker = $this;
        array_walk($plugins, function($plugin) use ($broker) {
            $broker->registerPlugin($plugin);
        });
        return $this;
    }

    public function registerPlugin(PluginInterface $plugin)
    {
        $key = self::pluginToKey($plugin);

        if ($this->contains($key)) {
            $this->registeredPluginException($key);
        }

        $this->plugins[$key] = $plugin;

        return $this;
    }

    public function unregisterPlugin(PluginInterface $plugin)
    {
        $key = self::pluginToKey($plugin);

        if (! $this->contains($key)) {
            $this->noPluginFoundException($key);
        }

        unset($this->plugins[$key]);
    }

    public function getPlugin($key)
    {
        if (! $this->contains($key)) {
            $this->noPluginFoundException($key);
        }
        return $this->plugins[$key];
    }

    public function plugin($key)
    {
        return $this->getPlugin($key);
    }

    public function isEmpty()
    {
        return empty($this->plugins);
    }

    public function contains($plugin)
    {
        return isset($this->plugins[self::pluginToKey($plugin)]);
    }

    public static function pluginToKey($plugin)
    {
        if ($plugin instanceof PluginInterface) {
            $plugin = get_class($plugin);
        }

        $key = implode('', array_slice(explode('\\', $plugin), -1));
        $key = strtolower(substr($key, 0, 1)) . substr($key, 1);

        return $key;
    }

    private function noPluginFoundException($plugin)
    {
        throw new \RuntimeException("No plugin '{$plugin}' was found.");
    }

    private function registeredPluginException($plugin)
    {
        throw new \RuntimeException("Plugin '{$plugin}' is already registered.");
    }
}
