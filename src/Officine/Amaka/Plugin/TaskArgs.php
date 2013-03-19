<?php

namespace Officine\Amaka\Plugin;

use Zend\Console\GetOpt;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\PluginInterface;

class TaskArgs implements PluginInterface
{
    private $opt;

    public function __construct(array $rules = array())
    {
        $this->opt = new GetOpt(array(), $rules);
    }

    public function __get($key)
    {
        return $this->opt->getOption($key);
    }

    public function __isset($key)
    {
        return $this->opt->__isset($key);
    }

    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(array($this->opt, $method), $arguments);
    }
}
