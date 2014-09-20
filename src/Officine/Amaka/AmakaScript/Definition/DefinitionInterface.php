<?php

namespace Officine\Amaka\AmakaScript\Definition;

interface DefinitionInterface
{
    public function getInvocable($invocable);
    public function hasInvocable($invocable);
    public function addInvocable($invocable);
    public function getDependencies($invocable);
}
