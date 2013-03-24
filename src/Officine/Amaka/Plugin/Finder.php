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
        if (! method_exists($this->finder, $method)) {
            throw new \BadMethodCallException("'{$method}' is not part of Symfony's Finder component.");
        }
        return call_user_func_array(array($this->finder, $method), $args);
    }
}
