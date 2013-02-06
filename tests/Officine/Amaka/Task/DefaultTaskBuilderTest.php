<?php

use Officine\Amaka\Task\DefaultTaskBuilder;

class DefaultTaskBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new DefaultTaskBuilder();
    }

    /**
     * @test
     */
    public function build_method_should_produce_task_objects()
    {
        $this->assertInstanceOf(
            'Officine\Amaka\Task\Task',
            $this->builder->build()
        );
    }

    /**
     * @test
     */
    public function should_use_setName_to_name_the_task()
    {
        $this->builder->setName(':name');
        $this->assertEquals(':name', $this->builder->build()->getName());
    }

    /**
     * @test
     */
    public function consecutive_calls_to_build_are_idempotent()
    {
        $this->assertSame(
            $this->builder->build(),
            $this->builder->build()
        );
    }
}
