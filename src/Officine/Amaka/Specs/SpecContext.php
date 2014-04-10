<?php

namespace Officine\Amaka\Specs;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\ContextInterface;

class SpecContext extends BehatContext
{
    private $subContexts = [];
    private $contextDirectory;

    const CONTEXT_DIR_KEY = 'context_directory';

    public function __construct(array $parameters = [])
    {
        !isset($parameters[self::CONTEXT_DIR_KEY]) ?: $this->setContextDirectory($parameters[self::CONTEXT_DIR_KEY]);
    }

    public function addSubcontext(ContextInterface $context)
    {
        $key = get_class($context);
        $this->subContexts[$key] = $context;

        return $this;
    }

    /**
     * This implementation of this method from the Behat\Context\ContextInterface
     * lookup any PHP file within the
     *
     */
    function getSubcontexts()
    {
        if (empty($this->subContexts) && isset($this->contextDirectory)) {
            $this->tryLoadingContextClasses();
        }
        return $this->subContexts;
    }

    function getSubcontextByClassName($className)
    {
        if (isset($this->subContexts[$className])) {
            return $this->subContexts[$className];
        }
    }

    public function setContextDirectory($contextDirectory)
    {
        $this->contextDirectory = $contextDirectory;
        return $this;
    }

    public function getContextDirectory()
    {
        return $this->contextDirectory;
    }

    public function tryLoadingContextClasses()
    {
        $op = \GlobIterator::CURRENT_AS_PATHNAME;
        $it = new \GlobIterator($this->contextDirectory . '/*Context.php', $op);
        foreach ($it as $filePath) {
            $className = substr($filePath, 1 + strrpos($filePath, '/'), -4);
            require_once $filePath;

            // linear search for the newly declared class.
            // while this might be slower than inflecting the class name
            // from the file name, we can instantiate the class also
            // when it has been declared in a namespace.
            foreach (get_declared_classes() as $currentClass) {
                if (strpos($currentClass, $className) !== false) {
                    $this->addSubcontext(new $currentClass([]));
                    break;
                }
            }
        }
    }
}
