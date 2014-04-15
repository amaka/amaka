<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use Officine\Amaka\Invocable;
use Officine\Amaka\AmakaScript\AmakaScript;

class AmakaScriptTest extends \PHPUnit_Framework_TestCase implements Invocable
{
    public function setUp()
    {
        $this->script = new AmakaScript();
    }

    /**
     * As a developer writing an amaka script using the APIs
     * I want to create a Buildfile object from an array definition
     * So that I can manipulate it before execution
     *
     * With "Buildfile manipulation" I mean adding/removing tasks to the
     * directly to it before processing it with a runner.
     *
     * @test
     */
    public function should_be_loadable_from_arrays()
    {
        $script = new AmakaScript();
        $script->loadFromArray(array());
    }

    /**
     * @test
     */
    public function should_be_manipulable_before_execution()
    {
        $arrayDefinition = array(new \Officine\Amaka\Task\Task('hello world'));

        $this->script->loadFromArray($arrayDefinition);
        $this->script->add(new \Officine\Amaka\Task\Task('example'));

        $this->assertTrue($this->script->has('hello world'));
        $this->assertTrue($this->script->has('example'));
    }

    /**
     * @test
     */
    public function should_alias_get_when_invoked_as_a_function()
    {
        $task = new \Officine\Amaka\Task\Task('example');
        $script = $this->script;

        $this->script->add($task);
        $this->assertSame($task, $script('example'));
    }

    /**
     * @test
     * @expectedException Officine\Amaka\ErrorReporting\Error
     */
    public function should_throw_when_creating_from_non_existing_file()
    {
        new AmakaScript(__DIR__ . '/_files/bogus/Amkfile');
    }

    /**
     * @test
     */
    public function should_create_configured_DefaultTaskBuilder()
    {
        $this->assertInstanceOf(
            '\Officine\Amaka\Task\DefaultTaskBuilder',
            $this->script->task('example')
        );
    }

    /**
     * @test
     */
    public function should_wrap_invocable_with_TaskBuilder()
    {
        $this->assertInstanceOf(
            '\Officine\Amaka\Task\DefaultTaskBuilder',
            $this->script->task($this)
        );
        $this->script->task($this);
    }

    public function hasInvoked()
    {
    }

    public function invoke()
    {
    }
}
