<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task;

use Officine\Amaka\Invocable;

/**
 * Task class
 *
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class Task implements Invocable
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

    /**
     * Invoke the task in order to trigger its function
     *
     * Remember to forward the call to this method if you don't want to
     * specify the invocation callback execution logic in your subclasses.
     */
    public function invoke()
    {
        // empty implementation provided.
        // TOCO: Abstract task?
    }
}
