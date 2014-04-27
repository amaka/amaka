<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task;

class TaskFactory
{
    /**
     * Create a new Task
     *
     * @param string $name
     * @return Officine\Amaka\Task\Task
     */
    public static function factory($name, $closure = null)
    {
        return new Task($name, $closure);
    }
}