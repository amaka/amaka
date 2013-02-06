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
    }

    // test amaka with contexts in a different test case

    public function testLoadingBuildfile()
    {
        $this->amaka->loadBuildfile('Amkfile');
    }

    /**
     * @expectedException Officine\Amaka\AmakaScript\AmakaScriptNotFoundException
     */
    public function testLoadingBuildfile2()
    {
        $this->amaka->loadBuildfile('AmkfileBogus');
    }

    public function testLoadBuildfileReturnsTheLoadedBuildfile()
    {
        $buildfile = $this->amaka->loadBuildfile('Amkfile');
        $this->assertSame($buildfile, $this->amaka->getBuildfile());
    }

    /**
     * @expectedException Officine\Amaka\AmakaScript\UndefinedTaskException
     */
    public function testAnExceptionIsThrownWhenRunningATaskThatIsNotDefinedInTheBuildfile()
    {
        $buildfile = $this->amaka->loadBuildfile('Amkfile');
        $this->assertNull($buildfile->get(':bogus-task'));
        $this->amaka->run(':bogus-task');
    }

    public function testRunningATask()
    {
        $buildfile = $this->amaka->loadBuildfile('Amkfile');
        $this->assertTrue($buildfile->has(':test'));
    }

    public function testLoadingABuildfileFromArray()
    {
        $this->amaka->loadBuildfile(array());
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
