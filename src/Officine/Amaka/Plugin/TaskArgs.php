<?php

namespace Officine\Amaka\Plugin;

use Zend\Console\Getopt;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\PluginInterface;

class TaskArgs extends GetOpt implements PluginInterface
{
    public function __construct($rules = array())
    {
        parent::__construct(array(), $rules);
    }
}
