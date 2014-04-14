<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\Invocable;
use Officine\Amaka\InvocablesList;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\Task\FileTaskBuilder;
use Officine\Amaka\Task\DefaultTaskBuilder;

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

    private $scriptId;

    private static $scriptIdCounter = 1;

    public function __construct($source = null)
    {
        $this->list = new InvocablesList();

        $this->fileBuilder = new FileTaskBuilder();
        $this->taskBuilder = new DefaultTaskBuilder();

        $this->scriptId = __CLASS__ . '#' . self::$scriptIdCounter++;

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
     *
     */
    public function loadFromFile($fileName)
    {
        if (! file_exists($fileName)) {
            throw new AmakaScriptNotFoundException($fileName);
        }

        $this->scriptId = $fileName;
        $amaka = $this;
        return $this->loadFromArray(include $fileName);
    }

    public function __toString()
    {
        return $this->scriptId;
    }
}
