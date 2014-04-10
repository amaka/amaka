<?php

use Officine\Amaka\Task\TaskFactory;

class TaskFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_provide_a_null_invocation_callback_by_default()
    {
        TaskFactory::factory(':foo');
    }

    // > Fail! TaskFactory::factory('')
    // > Fail! TaskFactory::factory(':')
    // > Okay! TaskFactory::factory('whatever', null)
    // > Okay! TaskFactory::factory('whatever', callable)

    /**
     * @test
     */
    public function should_only_accept_null_or_callable_invocation_callbacks()
    {
        TaskFactory::factory(':foo', 1);
    }

    /**
     * Rely only on class autoloading
     * Use a Task ClassNameResolver/Loader?
     *
     * @test
     */
    public function should()
    {
        TaskFactory::factory(':foo');
    }
}
