<?php

namespace Officine\Amaka\AmakaScript;

class DispatchTable
{
    private $operations = [];

    /**
     * @param string $operationName Should be a valid method name.
     */
    public function handle($operationName, $arguments = [])
    {
        if (empty($operationName)) {
            throw new \InvalidArgumentException('No operation name provided');
        }

        if (! $this->contains($operationName)) {
            throw new \Exception("Unknown operation '$operationName'");
        }

        $operation = $this->getOperation($operationName);

        if ($operation instanceof OperationInterface) {
            $operation = [$operation, 'invoke'];
        }
        return call_user_func_array($operation, $arguments);
    }

    public function getOperation($operationName)
    {
        if ($this->contains($operationName)) {
            return $this->operations[$operationName];
        }
    }

    public function expose($operationName, $closureHandler)
    {
        //if ($this->readOnly) {
        //    throw new \Exception();
        //}
        $this->operations[$operationName] = $closureHandler;
        return $this;
    }

    public function contains($operationName)
    {
        return isset($this->operations[$operationName]);
    }
}
