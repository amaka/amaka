<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task;

/**
 *
 *
 */
class TaskFactory
{
    /**
     * @var string
     */
    private static $namespace = '\\Officine\\Amaka\\Task\\Ext';

    /**
     * Create a new Task
     *
     * @param string $name
     * @return Officine\Amaka\Task\Task
     */
    public static function factory($name)
    {
        if (':' === substr($name, 0, 1)) {
            return new Task($name);
        }
        $class = self::$namespace . '\\' . ucfirst($name);
        return new $class($name);
    }
}