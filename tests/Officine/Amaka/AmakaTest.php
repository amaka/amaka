<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Ois\Tests\Amaka;

use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\Amaka;

/**
 * The Amaka class is a facade with a simple API to setup and
 * use an instance of Amaka programmatically.
 *
 * @group     amaka
 * @licese    http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaTest extends TestCase
{
    public function setUp()
    {
        $testContext = new \Officine\Amaka\Context();
        $testContext->setWorkingDirectory(__DIR__ . '/AmakaScript/_files');

        $this->amaka = new Amaka($testContext);
        $this->amaka->loadAmakaScript('Amkfile');
    }

    /**
     * @test
     */
    public function default_amaka_context_should_be_a_CliContext()
    {
        $amaka = new Amaka();
        $this->assertInstanceOf('Officine\Amaka\Context\CliContext', $amaka->getContext());
    }

    /**
     * @test
     */
    public function loading_amaka_script_resets_the_internal_instance()
    {
        $script = $this->amaka->loadAmakaScript('Amkfile');
        $this->assertSame($script, $this->amaka->getAmakaScript());

        $this->amaka->loadAmakaScript(array());
        $this->assertNotSame($script, $this->amaka->getAmakaScript());
    }

    /**
     * @test
     */
    public function taskSelector()
    {
        $script = $this->getMockBuilder('Officine\Amaka\AmakaScript\AmakaScript')
                       ->setMethods(array('has'))
                       ->getMock();
        $this->amaka->setAmakaScript($script);

        $this->assertFalse($this->amaka->taskSelector(null));

        $script->expects($this->any())
               ->method('has')
               ->with($this->equalTo(':default'))
               ->will($this->returnValue(false));

        $this->assertFalse($this->amaka->taskSelector(null));
        $this->assertFalse($this->amaka->taskSelector(':default'));
    }

    /**
     * @test
     */
    public function taskSelector2() {
        $script = $this->getMockBuilder('Officine\Amaka\AmakaScript\AmakaScript')
                       ->setMethods(array('has'))
                       ->getMock();

        $this->amaka->setAmakaScript($script);

        $script->expects($this->any())
               ->method('has')
               ->with($this->equalTo(':default'))
               ->will($this->returnValue(true));

        $this->assertEquals(':default', $this->amaka->taskSelector(null));
    }

    /**
     * @test
     */
    public function taskSelector3() {
        $script = $this->getMockBuilder('Officine\Amaka\AmakaScript\AmakaScript')
                       ->setMethods(array('has'))
                       ->getMock();

        $this->amaka->setAmakaScript($script);

        $script->expects($this->at(0))
               ->method('has')
               ->with($this->equalTo(':default'))
               ->will($this->returnValue(true));

        $script->expects($this->at(1))
               ->method('has')
               ->with($this->equalTo(':my-task'))
               ->will($this->returnValue(true));

        $this->assertEquals(':default', $this->amaka->taskSelector(null));
        $this->assertEquals(':my-task', $this->amaka->taskSelector(':my-task'));
    }

    public function testLoadingABuildfileFromArray()
    {
        $this->amaka->loadAmakaScript(array());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRunningEmptyTaskThrows()
    {
        $this->amaka->run('');
    }

    public function _testTaskPrerequisitesAreInvoked()
    {
        $task = $this->getMockBuilder('Officine\Amaka\Task\Task')
                     ->setConstructorArgs(array(':test'))
                     ->setMethods(array('invoke'))
                     ->getMock();

        $task->expects($this->once())
             ->method('invoke');

        $buildfile = $this->amaka->loadBuildfile('AmkfileWithPrerequisites');
        $buildfile[':test'] = $task;

        $this->amaka->run(':build');
    }
}
