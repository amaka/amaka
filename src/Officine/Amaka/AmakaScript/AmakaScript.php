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
use Officine\Amaka\Scope\ScriptScope;
use Officine\Amaka\ErrorReporting\Trigger;
use Officine\Amaka\AmakaScript\DispatchTable;
use Officine\Amaka\AmakaScript\Definition\ArrayDefinition;
use Officine\Amaka\AmakaScript\Definition\DefinitionInterface;

/**
 * @author Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaScript
{
    private $scriptFileName = 'NFF';
    private $scriptScope;
    private $helpersTable;
    private $operationsTable;
    private $scriptDefinition;

    public function __construct(DefinitionInterface $scriptDefinition = null)
    {
        $this->scriptDefinition = $scriptDefinition;
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

    public function getDefinition()
    {
        return $this->scriptDefinition;
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
        $this->scriptDefinition->fromArray($arrayDefinition);
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     */
    public function loadFromFile($fileName)
    {
        $__fileName = $fileName;
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

    public function __toString()
    {
        return $this->scriptFileName;
    }
}
