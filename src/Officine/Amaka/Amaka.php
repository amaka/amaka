<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
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

/**
 * Amaka Build Automation System Façade
 *
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class Amaka
{
    private $context;
    private $amakaScript;
    private $pluginBroker;
    private $defaultScriptName = 'Amkfile';

    public function __construct($defaultName = null, Context $context = null)
    {
        $this->setContext($context);
        $this->setDefaultScriptName($defaultName);

        $arguments = $this->getContext()
                          ->getParam('args');

        $baseDirectory = $this->getContext()
                              ->getWorkingDirectory();

        // this will be replaced with DI
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
    }

    private function setDefaultScriptName($defaultName = null)
    {
        if (null !== $defaultName) {
            $this->defaultScriptName = $defaultName;
        }
        return $this;
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

    public function getBuildfile()
    {
        return $this->amakaScript;
    }

    private function createAmakaScriptPath($script)
    {
        if ($this->getContext()->isAbsolutePath($script)) {
            return $script;
        }
        return $this->getContext()->getWorkingDirectory()
             . DIRECTORY_SEPARATOR
             . $script;
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
        if (null === $scriptName) {
            $scriptName = $this->defaultScriptName;
        }

        $scriptPath = $scriptName;
        if (is_string($scriptName)) {
            $scriptPath = $this->createAmakaScriptPath($scriptName);
        }
        $script = new AmakaScript();

        // we need to remove the coupling between Amaka, the PluginBroker and the AmakaScript classes
        // We'll refactor and apply IoC for the TaskBuilder to accept its own reference of the pluginBroker.
        $script->setPluginBroker($this->pluginBroker);
        $script->load($scriptPath);

        $this->setAmakaScript($script);

        return $script;
    }

    public function taskSelector($desiredTask = null)
    {
        $hasDefaultTask = $this->amakaScript->has(':default');
        // shortcircuit the call to has() when $desiredTask = null
        $hasDesiredTask = $desiredTask && $this->amakaScript->has($desiredTask);

        if ($desiredTask) {
            if ($hasDesiredTask) {
                return $desiredTask;
            }
            if ($hasDefaultTask) {
                return ':default';
            }
        }

        if ($hasDefaultTask) {
            return ':default';
        }

        return false;
    }

    /**
     * Run a task
     *
     * @param string $startTask The initial task we want to run
     */
    public function run($startTask)
    {
        $as = $this->amakaScript;
        $startTask = $this->taskSelector($startTask);

        if (false === $startTask) {
            throw new \RuntimeException("No task to run");
        }

        if (null === $as->get($startTask)) {
            throw new UndefinedTaskException(
                "Task '{$startTask}' was not defined in the amaka script."
            );
        }

        $detector = new CycleDetector($as);
        $runner   = new StandardRunner($as);

        if (! $detector->isValid($startTask)) {
            throw $detector->getExceptionClass();
        }

        $runner->run($startTask);
    }
}
