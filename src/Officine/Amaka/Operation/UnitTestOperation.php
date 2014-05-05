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

    public function invoke()
    {
        $args = func_get_args();
        $operationName = null;
        $configCallback = null;

        if (count($args) == 1 && is_string($args[0])) {
            $operationName = $args[0];
        } else if (count($args) == 1 && is_callable($args[0])) {
            $configCallback = $args[0];
        } else if (count($args) == 2 && is_string($args[0]) && 2 && is_callable($args[1])) {
            $operationName = $args[0];
            $configCallback = $args[1];
        }

        if ($operationName) {
            $this->operationName = $operationName;
        }

        if (is_callable($configCallback)) {
            $configCallback($this->testDriver);
        }

        $testDriver = $this->testDriver;
        return new Task('test', function() use ($testDriver) {
            $testDriver->run();
        });
    }

    public function getName()
    {
        return $this->operationName;
    }
}
