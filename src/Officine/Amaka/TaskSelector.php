<?php

namespace Officine\Amaka;

use Officine\Amaka\AmakaScript\Definition\DefinitionInterface;

class TaskSelector
{
    private $definition;
    private $defaultTaskName;

    public function __construct(DefinitionInterface $definition, $defaultTaskName = ':default')
    {
        $this->definition = $definition;
        $this->defaultTaskName = $defaultTaskName;
    }

    public function select($candidateTask)
    {
        $hasDefaultTask = $this->definition->hasInvocable($this->defaultTaskName);
        $hasDesiredTask = $candidateTask && $this->definition->hasInvocable($candidateTask);

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
}
