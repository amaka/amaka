<?php

use PHPUnit_Framework_TestCase as TestCase;

use Officine\StdLib\DynamicCall;

class DynamicCallTest extends TestCase
{
    public function testAttachingCallToClosure()
    {
        $closure = function () {
            return 'CALLED';
        };

        $call = new DynamicCall($closure);
        $this->assertInstanceOf('\\Closure', $closure);
        $this->assertEquals('CALLED', $closure());
        $this->assertEquals('CALLED', $call(), 'Could not get the result value of the invoked closure.');
    }

    public function call()
    {
        return 'CALLED';
    }

    public function testAttachingCallToMethod()
    {
        $scope = $this;
        $method = 'call';

        $call = new DynamicCall('call', $scope);
        $this->assertEquals('CALLED', $call(), 'Could not get the result value of the invoked method.');
    }

    public function testAttachingCallToFunction()
    {
        $function = create_function(null, 'return "CALLED";');
        $call = new DynamicCall($function);

        $this->assertEquals('CALLED', $call(), 'Could not get the result value of the invoked method.');
    }

    public function testPassingArgumentsToFunction()
    {
        $closure = function () {
            return implode('', func_get_args());
        };

        $call = new DynamicCall($closure);

        $this->assertEquals('CALLED', $closure('C', 'A', 'L', 'L', 'E', 'D'));
        $this->assertEquals('CALLED', $call('C', 'A', 'L', 'L', 'E', 'D'), 'Could not get the result value of the invoked closure.');
    }

    public function testCallingWithIncrementalArguments()
    {
        $arguments = ['C', 'A', 'L', 'L', 'E', 'D'];
        $tried = [];

        $closure = function () {
            return implode('', func_get_args());
        };

        // Rebuild incrementally the string 'CALLED'.
        while ($tried[] = array_shift($arguments)) {
            $call = new DynamicCall($closure);
            $call->setArgumentsAsArray($tried);

            $expectedReturnValue = $argumentsPassed = implode('', $tried);

            $this->assertEquals($expectedReturnValue, $call(), 'Could not call function with: ' . $argumentsPassed);
        }
    }
}
