<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use Officine\Amaka\Action\DependsOnAction;

use PHPUnit_Framework_TestCase as TestCase;

class DependsOnActionTest extends TestCase
{
    public function testDependenciesAreStoredInsideASymbolTable()
    {
        $invocable = $this->getMock('Officine\\Amaka\\Contrib\\NamedInvocable', ['getName', 'invoke']);
        $invocable->expects($this->once())
                  ->method('getName')
                  ->will($this->returnValue('A'));

        $table = $this->getMock('Officine\\Amaka\\AmakaScript\\SymbolTable', ['addSymbol']);
        $table->expects($this->once())
              ->method('addSymbol')
              ->with($this->equalTo('A'), $this->equalTo(['B', 'C']));

        $expr = new DependsOnAction($invocable, $table);
        $this->assertSame($expr, $expr->dependsOn('B', 'C'));
    }

    public function testExpressionBuilderForwardsCallToInvocableInvokeMethod()
    {
        $invocable = $this->getMock('Officine\\Amaka\\Contrib\\NamedInvocable', ['getName', 'invoke']);
        $invocable->expects($this->once())
                  ->method('invoke');

        $table = $this->getMock('Officine\Amaka\AmakaScript\SymbolTable');

        $expr = new DependsOnAction($invocable, $table);
        $expr->invoke();
    }
}
