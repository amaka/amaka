<?php

namespace Officine\Amaka\Task;

use Officine\Amaka\Invocable;
use Officine\Amaka\Context;

abstract class AbstractTask implements Invocable
{
    /**
     * Name of the task
     *
     * @var string
     */
    private $name;

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

    public function __toString()
    {
        return $this->getName();
    }
}
