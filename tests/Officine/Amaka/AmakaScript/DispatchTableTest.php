<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\AmakaScript\DispatchTable;

class DispatchTableTest extends TestCase
{
    public function testAddClosureHandlerOperation()
    {
        $table = new DispatchTable();
        $table->expose('op', function($arg1) {
            return $arg1;
        });
        $returnValue = $table->handle('op', ['foo']);
        $this->assertSame('foo', $returnValue);
    }

    public function testAddObjectHandlerOperation()
    {
        $operation = $this->getMock(
            '\Officine\Amaka\Operation\OperationInterface',
            ['invoke', 'getName']
        );
        $arguments = ['a', 1];
        $operation->expects($this->once())
                  ->method('invoke')
                  ->with(
                      $this->equalTo($arguments[0]),
                      $this->equalTo($arguments[1])
                  );

        $table = new DispatchTable();
        $table->expose('foo', $operation);
        $table->handle('foo', $arguments);
    }

    public function testArgumentsDefaultToEmptyArray()
    {
        $that = $this;

        $table = new DispatchTable();
        $table->expose('foo', function() use ($that) {
            $that->assertEmpty(func_get_args());
            return 'invoked';
        });

        $arguments = [new StdClass(), [], 'a', 1];
        $table->expose('bar', function() use ($that, $arguments) {
            $that->assertNotEmpty(func_get_args());
            $that->assertSame($arguments, func_get_args());
            return 'invoked';
        });

        $this->assertEquals('invoked', $table->handle('foo'));
        $this->assertEquals('invoked', $table->handle('bar', $arguments));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyStringIsAnInvalidOperationName()
    {
        $table = new DispatchTable();
        $table->handle('', []);
    }

    /**
     * @expectedException \Exception
     */
    public function testUnknownOperationNamesResultInAnExceptionBeingThrown()
    {
        $table = new DispatchTable();
        $table->handle('foo', []);
    }
}
