<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\Scope\TaskScope;

class TaskScopeTest extends TestCase
{
    public function testTaskScopeForwardsAllCallToDispatchTable()
    {
        $table = $this->getMock('Officine\Amaka\AmakaScript\DispatchTable', ['handle']);
        $table->expects($this->once())
              ->method('handle')
              ->with($this->equalTo('helperName'))
              ->will($this->returnValue('called'));

        $scope = new TaskScope($table);
        $this->assertEquals('called', $scope->helperName());
    }
}
