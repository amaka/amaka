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

    public function __construct(Context $context = null)
    {
        $this->setContext($context);

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

    /**
     * load the specified amaka script
     *
     * NOTE Do not call this method with a path to the script. Just
     * use the name.
     *
     * @param mixed $scriptNameOrArray The name of the script to load
     * @return Officine\Amaka\AmakaScript\AmakaScript
     */
    public function loadBuildfile($scriptNameOrArray)
    {
        if (is_string($scriptNameOrArray)) {
            $scriptNameOrArray = $this->createAmakaScriptPath($scriptNameOrArray);
        }

        $this->amakaScript = new AmakaScript();

        // pass the PluginBroker on to the amaka script
        $this->amakaScript->setPluginBroker($this->pluginBroker);

        // load the tasks from the amaka script

        $this->amakaScript->load($scriptNameOrArray);

        return $this->amakaScript;
    }

    /**
     * Run a task
     *
     * @param string $entry Starting task
     */
    public function run($entry)
    {
        if (empty($entry)) {
            throw new \RuntimeException("No task to run");
        }

        if (null === $this->amakaScript->get($entry)) {
            throw new UndefinedTaskException(
                "Task '{$entry}' was not defined in the amaka script."
            );
        }

        $detector = new CycleDetector($this->amakaScript);
        $runner   = new StandardRunner($this->amakaScript);

        if (! $detector->isValid($entry)) {
            throw $detector->getExceptionClass();
        }

        $runner->run($entry);
    }
}
