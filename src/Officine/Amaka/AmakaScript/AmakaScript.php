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
use Officine\Amaka\Task\Task;
use Officine\Amaka\PluginBroker;
use Officine\Amaka\ErrorReporting\Trigger;
use Officine\Amaka\Operation\TaskOperation;
use Officine\Amaka\Operation\FinderOperation;

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
    private $invocablesList;
    private $scriptScope;
    private $pluginBroker;
    private $symbolTable;
    private $dispatchTable;
    private $scriptFileName = 'NFF';

    public function __construct($source = null)
    {
        $this->invocablesList = new InvocablesList();

        $this->symbolTable = new SymbolTable();

        $this->dispatchTable = new DispatchTable();
        $this->dispatchTable->expose('task', new TaskOperation($this->symbolTable));
        $this->dispatchTable->expose('finder', new FinderOperation($this->symbolTable));

        $this->scriptScope = new ScriptScope($this->dispatchTable);

        $this->load($source);
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
    public function loadFromArray(array $arrayDefinition)
    {
        if (! empty($arrayDefinition)) {
            $symbols = $this->symbolTable;
            $invocables = $this->invocablesList;
            array_walk($arrayDefinition, function($invocable) use ($symbols, $invocables) {
                $invocables->add($invocable);
                $symbols->addSymbol(
                    $invocable->getName(),
                    $symbols->getSymbolsRequiredBy($invocable->getName())
                );
            });
        }
        //var_dump($this->symbolTable);
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     */
    public function loadFromFile($__fileName)
    {
        if (! file_exists($__fileName)) {
            $error = Trigger::fromException(new AmakaScriptNotFoundException($__fileName));
            $error->addResolution('Check')
                  ->trigger();
        }

        $amaka = $this->scriptScope;

        $fetchScriptStructure = function() use ($amaka, $__fileName) {
            return include $__fileName;
        };

        $bareScriptScope = $fetchScriptStructure->bindTo($this->scriptScope);
        $script = $bareScriptScope();

        $this->scriptFileName = $__fileName;
        return $this->loadFromArray(is_array($script) ? $script : []);
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
        return $this->invocablesList->get($element);
    }

    /**
     *
     */
    public function add($element)
    {
        return $this->invocablesList->add($element);
    }

    /**
     *
     */
    public function has($element)
    {
        return $this->invocablesList->contains($element);
    }

    public function getInvocables()
    {
        return $this->invocablesList;
    }

    public function getSymbolTable()
    {
        return $this->symbolTable;
    }

    public function isEmpty()
    {
        return $this->invocablesList->isEmpty();
    }

    /**
     * Return the list as the iterable object
     *
     * @return Officine\Amaka\InvocablesList
     */
    public function getIterator()
    {
        return $this->invocablesList;
    }

    public function setPluginBroker($broker)
    {
        $this->pluginBroker = $broker;
        return $this;
    }

    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }

    public function __toString()
    {
        return $this->scriptFileName;
    }
}
