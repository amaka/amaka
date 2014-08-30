<?php

namespace Officine\Amaka\AmakaScript\Definition;

use Officine\Amaka\InvocablesList;
use Officine\Amaka\AmakaScript\SymbolTable;

class ArrayDefinition extends InvocablesList implements DefinitionInterface
{
    private $dependencies;

    public function __construct(SymbolTable $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @param string|Officine\Amaka\Invocable
     */
    public function addInvocable($invocable)
    {
        return parent::add($invocable);
    }

    /**
     * @param string|Officine\Amaka\Invocable
     */
    public function getInvocable($invocable)
    {
        return parent::get($invocable);
    }

    /**
     * @param string|Officine\Amaka\Invocable
     */
    public function hasInvocable($invocable)
    {
        return parent::contains($invocable);
    }

    /**
     * @param string|Officine\Amaka\Invocable
     */
    public function getDependencies($invocable)
    {
        return $this->dependencies->getSymbolsRequiredBy($invocable);
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
        throw new \InvalidArgumentException("Empty invocable name or reference given.");
    }
}
