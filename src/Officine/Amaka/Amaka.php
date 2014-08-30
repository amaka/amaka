<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka;

use Officine\Amaka\TaskSelectork;
use Officine\Amaka\Context\CliContext;
use Officine\Amaka\AmakaScript\AmakaScript;
use Officine\Amaka\AmakaScript\Definition\ArrayDefinition;
use Officine\Amaka\AmakaScript\CycleDetector;
use Officine\Amaka\AmakaScript\SymbolTable;
use Officine\Amaka\AmakaScript\DispatchTable;
use Officine\Amaka\AmakaScript\StandardRunner;
use Officine\Amaka\AmakaScript\UndefinedTaskException;
use Officine\Amaka\AmakaScript\AmakaScriptNotFoundException;

use Officine\Amaka\Operation\TaskOperation;
use Officine\Amaka\Operation\UnitTestOperation;
use Officine\Amaka\Operation\UnitTest\PHPUnitDriver;

use Officine\Amaka\Plugin\Finder;
use Officine\Amaka\Plugin\TaskArgs;
use Officine\Amaka\Plugin\Directories;
use Officine\Amaka\Plugin\TokenReplacement;

use Officine\Amaka\ErrorReporting\Trigger;

/**
 * Amaka Build Automation System Façade
 *
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class Amaka
{
    private $context;
    private $amakaScript;
    private $symbolsTable;
    private $helpersTable;
    private $operationsTable;
    private $defaultScriptName = 'Amkfile';
    private $defaultTaskName = ':default';

    public function __construct($defaultName = null, Context $context = null)
    {
        $this->setContext($context);
        $this->setDefaultScriptName($defaultName);

        $arguments = $this->getContext()
                          ->getParam('args');

        $baseDirectory = $this->getContext()
                              ->getWorkingDirectory();

        $this->symbolsTable = new SymbolTable();
        $this->helpersTable = new DispatchTable();
        $this->operationsTable = new DispatchTable();

        $this->operationsTable->expose('task', new TaskOperation($this->helpersTable, $this->symbolsTable));
        $this->operationsTable->expose('unitTest', new UnitTestOperation(new PHPUnitDriver()));

        $this->helpersTable->expose('finder', function() {
            return new Finder();
        })->expose('directories', function() use ($baseDirectory) {
            return new Directories($baseDirectory);
        })->expose('taskArgs', function() use ($arguments) {
            return new TaskArgs($arguments);
        })->expose('tokenReplacement', function() {
            return new TokenReplacement();
        });
    }

    public function setContext(Context $context = null)
    {
        if ($context) {
            $this->context = $context;
        }
        return $this;
    }

    public function getContext()
    {
        // use the CliContext as default option
        if (null === $this->context) {
            $this->setContext(new CliContext());
        }

        return $this->context;
    }

    public function setAmakaScript(AmakaScript $script)
    {
        $this->amakaScript = $script;

        return $this;
    }

    public function getAmakaScript()
    {
        return $this->amakaScript;
    }

    /**
     * load the specified amaka script
     *
     * NOTE Do not call this method with a path to the script. Just
     * use the name.
     *
     * @param mixed $scriptNameOrArray The name of the script to load
     * @return Officine\Amaka\AmakaScript\AmakaScript
     */
    public function loadAmakaScript($scriptName = null)
    {
        if (! $scriptName) {
            $scriptName = $this->defaultScriptName;
        }
        $scriptPath = $this->createAmakaScriptPath($scriptName);
        $usingDefault = $scriptName == $this->defaultScriptName;

        if (! file_exists($scriptPath)) {
            $fail = Trigger::failure("Amaka script '$scriptPath' not found.");
            $fail->addResolution("Amaka can autoload a default script every time it's run.");
            $fail->addResolution('Run Amaka with the --init option to generate one from a template.');

            if ($usingDefault) {
                $fail->setMessage('Amaka could not find any script script to load');
            } else {
                $fail->addResolution("Or, make sure you've typed the path to the right file when using the -f option.");
            }
            $fail->trigger();
        }

        $definition = new ArrayDefinition($this->symbolsTable);

        $script = new AmakaScript($definition);
        $script->setHelpersTable($this->helpersTable)
               ->setOperationsTable($this->operationsTable)
               ->loadFromFile($scriptPath);

        $this->setAmakaScript($script);

        return $script;
    }

    /**
     * Run a task
     *
     * @param string $startTask The initial task we want to run
     */
    public function run($targetTask)
    {
        $definition = $this->amakaScript->getDefinition();

        $selector   = new TaskSelector($definition);
        $startTask  = $selector->select($targetTask);

        // The task selection logic returned false
        // no target task given by the user
        // and the script loaded was empty
        // Because one can't simply walk into Mordor.
        if (! $startTask && (! $targetTask || $definition->isEmpty())) {
            Trigger::error('No tasks to run')
                ->addResolution([
                    "Write a task called ':default' in your script:"
                    => "Next time Amaka is run without a target task "
                    .  "it will run this one instead of showing an error."
                ])->trigger();
        }

        // A target task was provided by the user,
        // but it could not be found inside the loaded script.
        // Perhaps the guy has mistyped its name, or something
        // we don't know.
        if ($targetTask && ! $definition->getInvocable($targetTask)) {
            Trigger::error()
                ->fromException(new UndefinedTaskException(
                    "Task '{$targetTask}' was not found in the amaka script."
                ))->addResolution("Check that you've typed the name of the task correcly.")
                ->trigger();
        }

        $detector = new CycleDetector($this->symbolsTable);
        $runner   = new StandardRunner($definition);

        if (! $detector->isValid($startTask)) {
            Trigger::error(
                'Cycle detected',
                "The execution was interrupted because '{$startTask}'"
                . " or one of its dependant tasks are creating a cycle."
            )->addResolution("Check if '{$startTask}' depends on itself.")
             ->addResolution("Check if any of the tasks required by '{$startTask}' either depend on '{$startTask}'.")
             ->trigger();
        }

        try {
            $runner->run($startTask);
        } catch (\Exception $e) {
            Trigger::error()
                ->fromException($e)
                ->trigger();
        }
    }

    /**
     * Change the default name used for amaka scripts
     *
     * @param string $defaultName
     * @return $this
     */
    private function setDefaultScriptName($defaultName = null)
    {
        if (null !== $defaultName) {
            $this->defaultScriptName = $defaultName;
        }
        return $this;
    }

    /**
     * Given a relative or absolute path to an amaka script this
     * method will convert it into an absolute path.
     *
     * When $scriptNameOrPath it's an absolute path to an amaka script
     * we allow the code to load the script even outside the working
     * directory declared with the context.
     *
     * @param string $scriptNameOrPath
     * @return string
     */
    private function createAmakaScriptPath($scriptNameOrPath)
    {
        if ($this->getContext()->isAbsolutePath($scriptNameOrPath)) {
            return $scriptNameOrPath;
        }
        return $this->getContext()->getWorkingDirectory()
             . DIRECTORY_SEPARATOR
             . $scriptNameOrPath;
    }
}
