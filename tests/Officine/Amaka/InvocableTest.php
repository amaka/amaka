<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use PHPUnit_Framework_TestCase as TestCase;
use Officine\Amaka\Invocable;

class InvocableTest extends TestCase
{
    public function setUp()
    {
        $this->invocable = new ReflectionClass('Officine\Amaka\Invocable');
    }

    public function testInvocableDeclaresInvokeMethod()
    {
        $this->assertTrue($this->invocable->hasMethod('invoke'));
    }
}