<?php

namespace Officine\Amaka\AmakaScript;

use Officine\StdLib\DynamicCall;
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

        if ($this->getMethod($methodName) instanceof OperationInterface) {
            $call = new DynamicCall('invoke', $this->getMethod($methodName));
            return $call->withArguments($arguments);
        }

        $call = new DynamicCall($this->getMethod($methodName));
        return $call->withArguments($arguments);
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
