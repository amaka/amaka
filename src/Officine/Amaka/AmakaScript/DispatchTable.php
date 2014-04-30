<?php

namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\Operation\OperationInterface;

class DispatchTable
{
    private $methods = [];

    /**
     * @param string $methodName Should be a valid method name.
     */
    public function handle($methodName, $arguments = [])
    {
        if (empty($methodName)) {
            throw new \InvalidArgumentException('No method name provided');
        }

        if (! $this->contains($methodName)) {
            throw new \Exception("Unknown method '$methodName'");
        }

        $method = $this->getMethod($methodName);

        if ($method instanceof OperationInterface) {
            $method = [$method, 'invoke'];
        }
        return call_user_func_array($method, $arguments);
    }

    public function getMethod($methodName)
    {
        if ($this->contains($methodName)) {
            return $this->methods[$methodName];
        }
    }

    public function expose($methodName, $closureHandler)
    {
        //if ($this->readOnly) {
        //    throw new \Exception();
        //}
        $this->methods[$methodName] = $closureHandler;
        return $this;
    }

    public function contains($methodName)
    {
        return isset($this->methods[$methodName]);
    }
}
