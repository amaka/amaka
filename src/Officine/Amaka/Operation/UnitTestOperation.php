<?php

namespace Officine\Amaka\Operation;

use Officine\Amaka\Task\Task;
use Officine\Amaka\Operation\UnitTest\TestDriverInterface;

class UnitTestOperation implements OperationInterface
{
    private $testDriver;
    private $operationName = 'test';

    public function __construct(TestDriverInterface $testDriver)
    {
        $this->testDriver = $testDriver;
    }


    public function invoke($operationName = null, $configCallback = null)
    {
        if (is_string($operationName)) {
            $this->operationName = $operationName;
        }

        if (is_callable($configCallback)) {
            $configCallback($this->testDriver);
        }

        $testDriver = $this->testDriver;
        return new Task($this->getName(), function() use ($testDriver) {
            $testDriver->run();
        });
    }

    public function getName()
    {
        return $this->operationName;
    }
}
