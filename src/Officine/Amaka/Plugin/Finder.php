<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginInterface;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder implements PluginInterface
{
    private $finder;

    public function __construct()
    {
        $this->finder = new SymfonyFinder();
    }

    public function __invoke()
    {
        return $this->finder;
    }

    public function __call($method, $args = array())
    {
        return call_user_func_array(array($this->finder, $method), $args);
    }
}
