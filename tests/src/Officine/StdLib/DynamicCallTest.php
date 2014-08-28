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
        $this->assertEquals('CALLED', $call('C', 'A', 'L', 'L', 'E', 'D'), 'Could not ensure the arguments were passed to the invoked closure.');
    }

    public function testCallingWithArguments()
    {
        $arguments = ['C', 'A', 'L', 'L', 'E', 'D'];

        $closure = function () {
            return implode('', func_get_args());
        };
        $call = new DynamicCall($closure);

        $this->assertEquals('CALLED', $call('C', 'A', 'L', 'L', 'E', 'D'));
    }

    public function multipleArgumentsProvider()
    {
        return [
            ['', ['']],
            ['C', ['C']],
            ['CA', ['C', 'A']],
            ['CAL', ['C', 'A', 'L']],
            ['CALL', ['C', 'A', 'L', 'L']],
            ['CALLE', ['C', 'A', 'L', 'L', 'E']],
            ['CALLED', ['C', 'A', 'L', 'L', 'E', 'D']],
        ];
    }

    /**
     * @dataProvider multipleArgumentsProvider
     */
    public function testCallingMultipleArguments($expected, $actual)
    {
        $call = new DynamicCall('call', $this);
        $this->assertEqual($expected, $actual);
    }

}
