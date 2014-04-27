<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task;

/**
 * Task class
 *
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class Task extends AbstractTask
{
    private $codeFragment;

    public function __construct($name, $closure = null)
    {
        $this->codeFragment = $closure;
        parent::__construct($name);
    }

    public function invoke()
    {
        if (! is_callable($this->codeFragment)) {
            return;
        }

        $arguments = func_get_args();
        return call_user_func_array($this->codeFragment, $arguments);
    }
}
