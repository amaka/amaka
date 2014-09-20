<?php

namespace Officine\Amaka\AmakaScript\Definition;

use Officine\Amaka\Contrib\Invocable;
use Officine\Amaka\AmakaScript\SymbolTable;
use Officine\Amaka\AmakaScript\Definition\ArrayDefinition;

class ArrayDefinition extends \ArrayObject implements DefinitionInterface
{
    private $dependencies;

    public function __construct(SymbolTable $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @param string|Officine\Amaka\Contrib\Invocable
     */
    public function addInvocable($invocable)
    {
        $this->offsetSet(self::elementKey($invocable), $invocable);
        return $this;
    }

    /**
     * @param string|Officine\Amaka\Contrib\Invocable
     */
    public function getInvocable($invocable)
    {
        if ($this->offsetExists(self::elementKey($invocable))) {
            return $this->offsetGet(self::elementKey($invocable));
        }
        return null;
    }

    /**
     * @param string|Officine\Amaka\Contrib\Invocable
     */
    public function hasInvocable($invocable)
    {
        return $this->offsetExists(self::elementKey($invocable));
    }

    /**
     * Remove an element from the list
     *
     * @param Invocable $invocable
     */
    public function removeInvocable($invocable)
    {
        $this->offsetUnset(self::elementKey($invocable));
        return $this;
    }

    /**
     * Return the first Invocable in the list
     *
     * @return Invocable
     */
    public function first()
    {
        $invs = $this->getArrayCopy();
        return reset($invs);
    }

    /**
     * Return the last Invocable in the list
     *
     * @return Invocable
     */
    public function last()
    {
        $invs = $this->getArrayCopy();
        return end($invs);
    }

    /**
     * @param string|Officine\Amaka\Contrib\Invocable
     */
    public function getDependencies($invocable)
    {
        return $this->dependencies->getSymbolsRequiredBy($invocable);
    }

    /**
     * If the list has no elements this method returns false.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return 0 == $this->count();
    }

    public function fromArray(array $arrayDefinition)
    {
        $definition = $this;
        $dependencies = $this->dependencies;

        if (! empty($arrayDefinition)) {
            array_walk($arrayDefinition, function($invocable) use ($definition, $dependencies) {
                $definition->addInvocable($invocable);
                $dependencies->addSymbol(
                    $invocable->getName(),
                    $dependencies->getSymbolsRequiredBy($invocable->getName())
                );
            });
        }

        return $this;
    }

    /**
     * Converts an element into its identifying name.
     *
     * The element need not be in the list to get its name.
     *
     * This function doesn't check if $invocable is contained in the
     * list, or if the string given is the name of an actual element
     * in the list.
     *
     * @param mixed $invocable
     * @return string
     * @throws possibly @todo move this validation somewhere more appropriate.
     */
    public static function elementKey($invocable)
    {
        if (is_string($invocable)) {
            return $invocable;
        }
        if ($invocable instanceof Invocable) {
            return $invocable->getName();
        }
        throw new \InvalidArgumentException("Invalid invocable name or reference given.");
    }
}
