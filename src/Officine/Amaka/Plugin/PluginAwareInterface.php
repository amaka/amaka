<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginBroker;

interface PluginAwareInterface
{
    public function plugin($plugin);
    public function getPluginBroker();
    public function setPluginBroker(PluginBroker $broker);
}
