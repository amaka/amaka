<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\AmakaScript\ScriptScope;

class ScriptScopeTest extends TestCase
{
    public function testCallingAnOperationExposedThroughTheDispatchTable()
    {
        $closure = function() {};
        $table = $this->getMock('Officine\Amaka\AmakaScript\DispatchTable', ['handle']);
        $table->expects($this->once())
              ->method('handle')
              ->with(
                  $this->equalTo('task'),
                  [':foo', $closure]
              )->will($this->returnValue(true));

        $scope = new ScriptScope($table);
        $returnValueIsCaptured = $scope->task(':foo', $closure);

        $this->assertTrue($returnValueIsCaptured);
    }
}
