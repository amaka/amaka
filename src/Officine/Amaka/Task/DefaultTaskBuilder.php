<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Task;

use Officine\Amaka\Invocable;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\Plugin\PluginAwareInterface;

/**
 * This is the default task builder implementation
 *
 * The task builder is the interface that is actually used by the
 * end user (inside the buildfile/amaka scripts) to create the needed
 * task objects. Methods like dependsOn are special configuration methods
 * that act before the Task object is created calling the #build()# method.
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class DefaultTaskBuilder implements Invocable
{
    /**
     * The name we are going to assign to the task
     *
     * @var string
     */
    protected $name = ':unnamed';

    /**
     * The invocation callback that we are going to attach to the task
     *
     * @var callable
     */
    protected $callback;

    /**
     * Task Adjacency List
     *
     * @array
     */
    protected $adjacencyList = array();

    /**
     * The task once built
     *
     * @var Officine\Amaka\Invocable
     */
    protected $task;

    /**
     * Plugin Broker
     *
     */
    protected $pluginBroker;

    public function setTask(Invocable $task = null)
    {
        $this->task = $task;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPluginBroker(PluginBroker $broker)
    {
        $this->pluginBroker = $broker;

        return $this;
    }

    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }

    public function plugin($plugin)
    {
        return $this->pluginBroker->plugin($plugin);
    }

    /**
     * Set the task invocation callback
     *
     * @param callable $callback
     * @return self
     */
    public function setInvocationCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Return the invocation callback
     *
     * @return callable
     */
    public function getInvocationCallback()
    {
        return $this->callback;
    }

    /**
     * Create and return the Task object using the information collected
     * until the call to #build()# was made.
     *
     * NOTE: This method uses the newly added TaskFactory class to
     * defer the decision of which class we want to instantiate.
     *
     * @return Officine\Amaka\Task
     */
    public function build()
    {
        if (null === $this->task) {
            $this->task = TaskFactory::factory($this->name);
        }
        return $this->task;
    }

    public function invoke()
    {
        $task = $this->build();

        if ($task instanceof PluginAwareInterface) {
            $task->setPluginBroker($this->getPluginBroker());
        }

        // TODO: the following code is an hotfix added for the feature
        // 'Invocables with factory' to work. No test case have been
        // updated to cover this patch.
        if (is_callable($this->getInvocationCallback())) {
            $ic = $this->getInvocationCallback();
            call_user_func_array($ic, array($this));
        }
        $task->invoke();
    }

    /**
     * This method will forward all unrecognized messages to the
     * actual task object.
     *
     */
    public function __call($method, $args = array())
    {
        if ($this->task && method_exists($this->task, $method)) {
            return call_user_func_array(array($this->task, $method), $args);
        }
    }

    /**
     *
     * @param variadic comma separated list of task names as strings.
     */
    public function dependsOn()
    {
        $tasks = func_get_args();

        // dependsOn must be called with at least one parameter
        if (empty($tasks)) {
            throw new \BadMethodCallException();
        }

        foreach ($tasks as $task) {
            if (! is_string($task)) {
                throw new \InvalidArgumentException('Only strings are allowed to declare dependencies.');
            }
            // TOCO: if we allowed Invocables to be defined in
            // dependsOn?  that would allow for anonymous tasks, or at
            // least be useful for directly instantiating Invocables
            // without declaring them first (drawback: no
            // configuration! Unless one uses the task() helper
            // method). i.e. dependsOn(new whatever())

            // we're pushing the task into the adjacency list (i.e. array)
            // which will be used to build an acyclic directed graph to determine
            // the order of execution of the tasks.
            if (! in_array($task, $this->adjacencyList)) {
                $this->adjacencyList[] = $task;
            }
        }

        return $this;
    }

    /**
     * Return the list of dependencies of the task
     *
     * @return array
     */
    public function getAdjacencyList()
    {
        return $this->adjacencyList;
    }
}
