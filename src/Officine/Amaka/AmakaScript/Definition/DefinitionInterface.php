<?php

namespace Officine\Amaka\AmakaScript\Definition;

use Officine\Amaka\InvocableInterface;

interface DefinitionInterface
{
    public function getInvocable($invocable);
    public function hasInvocable($invocable);
    public function addInvocable($invocable);
    public function getDependencies($invocable);
}