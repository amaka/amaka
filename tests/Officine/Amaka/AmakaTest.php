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
     * @Given an Amaka Script with no tasks
     * @When selecting a runnable task
     * @Then the taskSelector will always yield false
     *
     * @test
     */
    public function taskSelector_yields_false_when_neither_default_nor_desired_task_are_present()
    {
        $script = $this->getMock('Officine\Amaka\AmakaScript\AmakaScript');

        $this->amaka->setAmakaScript($script);

        $this->assertFalse($this->amaka->taskSelector(null));
        $this->assertFalse($this->amaka->taskSelector(':default'));
        $this->assertFalse($this->amaka->taskSelector(':not-here'));
        $this->assertFalse($this->amaka->taskSelector(':my-task'));
    }

    /**
     * @Given an Amaka Script with a only a task called ':default'
     * @When selecting a runnable task
     * @Then the taskSelector will yield ':default' when called with no arguments
     * @And the taskSelector will yield ':default' when called with any argument
     *
     * @test
     */
    public function taskSelector_yields_default_task() {
        $script = $this->getMockBuilder('Officine\Amaka\AmakaScript\AmakaScript')
                       ->setMethods(array('has'))
                       ->getMock();

        $this->amaka->setAmakaScript($script);

        // has default (w/ null argument)
        $script->expects($this->at(0))
               ->method('has')
               ->will($this->returnValue(true));

        // has desired (w/ null argument)
        $script->expects($this->at(1))
               ->method('has')
               ->will($this->returnValue(true));

        // has default (w/ any argument)
        $script->expects($this->at(2))
               ->method('has')
               ->will($this->returnValue(true));

        // has desired (w/ any argument)
        $script->expects($this->at(3))
               ->method('has')
               ->will($this->returnValue(false));

        $this->assertEquals(':default', $this->amaka->taskSelector(null));
        $this->assertEquals(':default', $this->amaka->taskSelector(':my-task'));
    }

    /**
     * @Given an Amaka Script with two tasks called ':default' and ':my-task'
     * @When selecting a runnable task
     * @Then the taskSelector will yield ':default' when called with no arguments
     * @And the taskSelector will yield ':default' when called with ':default' as argument
     * @And the taskSelector will yield ':my-task' when called with ':my-task' as argument
     *
     * @test
     */
    public function taskSelector3() {
        $script = $this->getMockBuilder('Officine\Amaka\AmakaScript\AmakaScript')
                       ->setMethods(array('has'))
                       ->getMock();

        $this->amaka->setAmakaScript($script);

        $script->expects($this->any())
               ->method('has')
               ->will($this->returnValue(true));

        $this->assertEquals(':default', $this->amaka->taskSelector(null));
        $this->assertEquals(':default', $this->amaka->taskSelector(':default'));

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
