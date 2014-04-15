<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka;

use Zend\EventManager\EventManager;

use Officine\Amaka\Context\CliContext;
use Officine\Amaka\AmakaScript\CycleDetector;
use Officine\Amaka\AmakaScript\StandardRunner;
use Officine\Amaka\AmakaScript\AmakaScript;
use Officine\Amaka\AmakaScript\UndefinedTaskException;
use Officine\Amaka\AmakaScript\AmakaScriptNotFoundException;

use Officine\Amaka\PluginBroker;
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
    private $pluginBroker;
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

        // IoC should be applied to the code below this marker line
        $broker = new PluginBroker();
        $plugins = array(
            new Finder(),
            new TaskArgs($arguments),
            new Directories($baseDirectory),
            new TokenReplacement(),
        );

        array_walk($plugins, function($plugin) use ($broker) {
            $broker->registerPlugin($plugin);
        });

        $this->setPluginBroker($broker);
        // end of marker
    }

    public function setPluginBroker($broker)
    {
        $this->pluginBroker = $broker;
        return $this;
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
        // we need to remove the coupling between Amaka, the PluginBroker and the AmakaScript classes
        // We'll refactor and apply IoC for the TaskBuilder to accept its own reference of the pluginBroker.
        $script = new AmakaScript();
        $script->setPluginBroker($this->pluginBroker);
        $script->loadFromFile($scriptPath);

        $this->setAmakaScript($script);

        return $script;
    }

    /**
     * Use this method when you need to know and select the task name
     * to pass to the run method.
     *
     * When $candidateTask is registered in the script this method
     * will return exaclty $candidateTask, meaning you can run that
     * task without further tests.  On the other hand when
     * $candidateTask isn't registered, but a default task is present,
     * the name of the default task is returned. When neither the
     * $candidateTask nor the default one are registered in the script
     * the method returns false.
     *
     * <code>
     *   $amaka->loadAmakaScript(...);
     *   $taskToRun = $amaka->taskSelector(':my-task');
     *
     *   $amaka->run($taskToRun);
     * </code>
     *
     * @param string $candidateTask [optional]
     * @return string|false
     */
    public function taskSelector($candidateTask = false)
    {
        $hasDefaultTask = $this->amakaScript->has($this->defaultTaskName);
        $hasDesiredTask = $candidateTask && $this->amakaScript->has($candidateTask);

        if ($candidateTask) {
            if ($hasDesiredTask) {
                return $candidateTask;
            }
        }

        if ($hasDefaultTask) {
            return $this->defaultTaskName;
        }

        return false;
    }

    /**
     * Run a task
     *
     * @param string $startTask The initial task we want to run
     */
    public function run($selectedStartTask)
    {
        $as = $this->amakaScript;
        $startTask = $this->taskSelector($selectedStartTask);

        if (! $startTask && (! $selectedStartTask || $as->isEmpty())) {
            $error = Trigger::error('No tasks to run');
            $error->addResolution("You could declare a ':default' in the script.");
            $error->trigger();
        }

        if ($selectedStartTask && ! $as->get($selectedStartTask)) {
            $error = Trigger::error();
            throw new UndefinedTaskException(
                "Task '{$selectedStartTask}' was not found in the amaka script."
            );
        }

        $detector = new CycleDetector($as);
        $runner   = new StandardRunner($as);

        if (! $detector->isValid($startTask)) {
            throw $detector->getExceptionClass();
        }

        $runner->run($startTask);
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
