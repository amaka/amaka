<?php

use Officine\Amaka\Task\DefaultTaskBuilder;

class DefaultTaskBuilderDependsOnTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new DefaultTaskBuilder();
    }

    /**
     * @expectedException \BadMethodCallException
     * @test
     */
    public function should_throw_when_passing_zero_args()
    {
        $this->builder->dependsOn();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @test
     */
    public function should_throw_when_passing_non_string_args()
    {
        $this->builder->dependsOn(1, 'B');
    }

    /**
     * @test
     */
    public function should_accept_task_names()
    {
        $this->builder->dependsOn(':task-name');
    }

    /**
     * @test
     */
    public function should_accept_multiple_arguments()
    {
        $this->builder->dependsOn(':first-task', ':second-task');
    }

    /**
     * @test
     */
    public function calls_should_be_chainable()
    {
        $this->assertSame(
            $this->builder,
            $this->builder->dependsOn('foobar')
        );
    }

    /**
     * @test
     */
    public function should_retun_task_adjacency_list()
    {
        $this->assertNotNull($this->builder->getAdjacencyList());
    }

    /**
     * @test
     */
    public function adjacency_list_should_contain_the_required_tasks()
    {
        $this->builder->dependsOn('B', 'C');
        $this->assertContains('B', $this->builder->getAdjacencyList());
        $this->assertContains('C', $this->builder->getAdjacencyList());
    }

    /**
     * @test
     */
    public function adjacency_list_should_not_store_duplicate_values()
    {
        $this->builder->dependsOn('B');
        $this->builder->dependsOn('B', 'B');

        $this->assertEquals(1, count($this->builder->getAdjacencyList()));
    }
}
