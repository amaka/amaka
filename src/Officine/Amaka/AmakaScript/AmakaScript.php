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
use Officine\Amaka\Scope\ScriptScope;
use Officine\Amaka\ErrorReporting\Trigger;
use Officine\Amaka\AmakaScript\SymbolTable;
use Officine\Amaka\AmakaScript\DispatchTable;

/**
 * @author Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaScript implements \IteratorAggregate
{
    private $scriptFileName = 'NFF';
    private $scriptScope;
    private $symbolsTable;
    private $helpersTable;
    private $operationsTable;
    private $invocablesList;

    public function __construct($source = null)
    {
        $this->invocablesList = new InvocablesList();

        $this->load($source);
    }

    public function setSymbolsTable(SymbolTable $symbolsTable)
    {
        $this->symbolsTable = $symbolsTable;
        return $this;
    }

    public function setHelpersTable(DispatchTable $helpersTable)
    {
        $this->helpersTable = $helpersTable;
        return $this;
    }

    public function setOperationsTable(DispatchTable $operationsTable)
    {
        $this->operationsTable = $operationsTable;
        return $this;
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
            $symbols = $this->symbolsTable;
            $invocables = $this->invocablesList;
            array_walk($arrayDefinition, function($invocable) use ($symbols, $invocables) {
                $invocables->add($invocable);
                $symbols->addSymbol(
                    $invocable->getName(),
                    $symbols->getSymbolsRequiredBy($invocable->getName())
                );
            });
        }
        //var_dump($this->symbolsTable);
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
        $__dirName = dirname($__fileName);

        $scope = new ScriptScope($this->operationsTable);
        $amaka = $scope;

        $fetcher = function() use ($amaka, $__dirName, $__fileName) {
            return include $__fileName;
        };

        $newScope = $fetcher->bindTo($scope);
        $scriptArrayDefinition = $newScope();

        $this->scriptScope = $scope;
        $this->scriptFileName = $__fileName;

        if (is_array($scriptArrayDefinition)) {
            return $this->loadFromArray($scriptArrayDefinition);
        }
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
        return $this->symbolsTable;
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

    public function __toString()
    {
        return $this->scriptFileName;
    }
}
