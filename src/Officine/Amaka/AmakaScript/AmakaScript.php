<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\Invocable;
use Officine\Amaka\InvocablesList;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\Task\FileTaskBuilder;
use Officine\Amaka\Task\DefaultTaskBuilder;
use Officine\Amaka\ErrorReporting\Trigger;

/**
 * @author Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaScript implements \IteratorAggregate
{
    /**
     * The set of vertices making up the Buildfile DAG, each vertex is
     * an Invocable object, e.g. tasks, registred in the amaka
     * scripts.
     *
     * @var Officine\Amaka\InvocablesList
     */
    private $list;

    /**
     * Default task builder implementation
     *
     * TODO: Replace Prototype with Abstract Factory
     *
     * @var Officine\Amaka\Task\DefaultTaskBuilder
     */
    private $taskBuilder;

    private $scriptFileName = 'NFF';

    public function __construct($source = null)
    {
        $this->list = new InvocablesList();

        $this->fileBuilder = new FileTaskBuilder();
        $this->taskBuilder = new DefaultTaskBuilder();

        $this->load($source);
    }

    public function setTaskBuilder(DefaultTaskBuilder $builder)
    {
        $this->taskBuilder = $builder;

        return $this;
    }

    public function getTaskBuilder()
    {
        return $this->taskBuilder;
    }

    public function setFileTaskBuilder($builder)
    {
        $this->fileBuilder = $builder;

        return $this;
    }

    public function getFileTaskBuilder()
    {
        return $this->fileBuilder;
    }

    /**
     * This will be removed once Di is added to the code
     * @deprecated in next version
     */
    public function setPluginBroker(PluginBroker $broker)
    {
        $this->getTaskBuilder()->setPluginBroker($broker);
        $this->getFileTaskBuilder()->setPluginBroker($broker);
    }

    /**
     *
     */
    public function __invoke($element)
    {
        return $this->get($element);
    }

    /**
     *
     */
    public function get($element)
    {
        return $this->list->get($element);
    }

    /**
     *
     */
    public function add($element)
    {
        return $this->list->add($element);
    }

    /**
     *
     */
    public function has($element)
    {
        return $this->list->contains($element);
    }

    public function isEmpty()
    {
        return $this->list->isEmpty();
    }

    /**
     * Return the list as the iterable object
     *
     * @return Officine\Amaka\InvocablesList
     */
    public function getIterator()
    {
        return $this->list;
    }

    /**
     * Load a buildfile from an array or file
     */
    public function load($fileOrArray)
    {
        if (null === $fileOrArray) {
            return;
        }

        if (is_array($fileOrArray)) {
            return $this->loadFromArray($fileOrArray);
        }
        return $this->loadFromFile($fileOrArray);
    }

    /**
     * Iteratively adds the elements in #$array# to the Buildfile
     *
     * This method is intended for internal use within the
     * loadFromFile, any use form other client classes is discouraged.
     *
     * @param array $array
     * @return $this
     */
    public function loadFromArray(array $array)
    {
        if (! empty($array)) {
            $buildfile = $this;
            array_map(function($invocable) use ($buildfile) {
                $buildfile->add($invocable);
            }, $array);
        }

        return $this;
    }

    /**
     * Create and return a built-in or action task.
     *
     * @param Invocable|string $task
     * @param callable $ic
     */
    public function task($task, $ic = null)
    {
        $builder = clone $this->getTaskBuilder();

        if ($task instanceof Invocable) {
            $builder->setTask($task);
            $builder->setName($task->getName());
        } else {
            $builder->setName($task);
        }
        $builder->setInvocationCallback($ic);

        return $builder;
    }

    /**
     * Create and returns a File task.
     *
     * @param string $filename
     * @param callable $ic
     */
    public function file($filename, $ic = null)
    {
        $builder = clone $this->getFileTaskBuilder();

        $builder->setInvocationCallback($ic);
        $builder->setName($filename);

        return $builder;
    }

    /**
     *
     */
    public function loadFromFile($__fileName)
    {
        if (! file_exists($__fileName)) {
            $error = Trigger::fromException(new AmakaScriptNotFoundException($__fileName));
            $error->addResolution('Check')
                  ->trigger();
        }
        $amaka = $this;
        $fetchScriptStructure = function() use ($amaka, $__fileName) {
            return include $__fileName;
        };

        $table = new DispatchTable();
        $scriptScope = new ScriptScope($table);

        $table->expose('task', [$this, 'task']);

        $bareScriptScope = $fetchScriptStructure->bindTo($scriptScope);
        $script = $bareScriptScope();

        $this->scriptFileName = $__fileName;
        return $this->loadFromArray(is_array($script) ? $script : []);
    }

    public function __toString()
    {
        return $this->scriptFileName;
    }
}
