<?php

namespace Officine\Test\Amaka\Operation;

use PHPUnit_Framework_TestCase as TestCase;
use Officine\Amaka\Operation\UnitTestOperation;

class UnitTestTest extends TestCase
{
    public function setUp()
    {
        $this->mockDriver = $this->getMock('Officine\Amaka\Operation\UnitTest\TestDriverInterface');
    }

    public function testDefaultNameShouldBeTest()
    {
        $operation = new UnitTestOperation($this->mockDriver);
        $this->assertEquals("test", $operation->getName());
    }

    public function testShouldAcceptJustConfigCallback()
    {
        $operation = new UnitTestOperation($this->mockDriver);
        $operation->invoke(function() {
        });
        $this->assertEquals("test", $operation->getName());
    }

    public function testShouldAllowOverridingDefaultName()
    {
        $operation = new UnitTestOperation($this->mockDriver);
        $operation->invoke("new-name");

        $this->assertEquals("new-name", $operation->getName());
    }

    public function testShouldAcceptTaskNameAndConfigCallback()
    {
        $operation = new UnitTestOperation($this->mockDriver);
        $operation->invoke("new-name", function() {
        });

        $this->assertEquals("new-name", $operation->getName());
    }

    public function testInvokingUnitTestWillReturnedATask()
    {
        $operation = new UnitTestOperation($this->mockDriver);
        $task = $operation->invoke();

        $this->assertInstanceOf('Officine\Amaka\Task\Task', $task);
    }

    public function testDriverRunMethodIsCalledAtTheTaskInvocationTime()
    {
        $this->mockDriver->expects($this->once())
                         ->method('run');

        $operation = new UnitTestOperation($this->mockDriver);
        $task = $operation->invoke();
        $task->invoke();
    }

    public function testConfigCallbackIsCalledBeforeTheRunMethod()
    {
        $this->mockDriver->expects($this->any())
                         ->method('run')
                         ->will($this->onConsecutiveCalls('first', 'invalid'));

        $assert = $this;
        $operation = new UnitTestOperation($this->mockDriver);
        $task = $operation->invoke(null, function($driver) use ($assert) {
            $assert->assertEquals('first', $driver->run());
        });
        $task->invoke();
    }
}
