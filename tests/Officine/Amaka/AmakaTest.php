<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
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
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaTest extends TestCase
{
    const CONTEXT_CLASS = 'Officine\\Amaka\\Context';

    public function setUp()
    {
        $context = $this->getMockBuilder(self::CONTEXT_CLASS)
                        ->setMethods(['getWorkingDirectory'])
                        ->getMock();

        $context->expects($this->any())
                ->method('getWorkingDirectory')
                ->will($this->returnValue(__DIR__ . '/AmakaScript/_files'));

        $this->amaka = new Amaka(null, $context);
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

    public function testLoadingABuildfileFromArray()
    {
        $this->amaka->loadAmakaScript(array());
    }

    /**
     * @expectedException \Officine\Amaka\ErrorReporting\Error
     */
    public function testRunningEmptyTaskThrows()
    {
        $this->amaka->run(null);
    }

    public function _testTaskPrerequisitesAreInvoked()
    {
        $task = $this->getMockBuilder('Officine\Amaka\Task\Task')
                     ->setConstructorArgs(array(':test'))
                     ->setMethods(array('invoke'))
                     ->getMock();

        $task->expects($this->once())
             ->method('invoke');

        $buildfile = $this->amaka->loadAmakaScript('AmkfileWithPrerequisites');
        $buildfile[':test'] = $task;

        $this->amaka->run(':build');
    }
}
