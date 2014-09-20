<?php

use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\TaskSelector;

class TaskSelectorTest extends TestCase
{
    const DEFINITION_CLASS = 'Officine\\Amaka\\AmakaScript\\Definition\\DefinitionInterface';

    /**
     * @Given an Amaka Script with no tasks
     * @When selecting a runnable task
     * @Then the taskSelector will always yield false
     */
    public function testYieldsFalseWhenNoTasksArePresent()
    {
        $definition = $this->getDefinitionMock();
        $selector = new TaskSelector($definition);

        $definition->expects($this->any())
                   ->method('hasInvocable')
                   ->will($this->returnValue(false));

        $this->assertFalse($selector->select(null));
        $this->assertFalse($selector->select(':default'));
        $this->assertFalse($selector->select(':not-here'));
        $this->assertFalse($selector->select(':my-task'));
    }

    public function testYieldsDefaultTaskNameWhenDefaultTaskIsPresent()
    {
        $definition = $this->getDefinitionMock();
        $selector = new TaskSelector($definition);

        $definition->expects($this->any())
                   ->method('hasInvocable')
                   ->will($this->returnValue(true));

        $this->assertEquals(':default', $selector->select(null));
        $this->assertEquals(':default', $selector->select(':default'));
    }

    public function testDefaultTaskSelection()
    {
        $definition = $this->getDefinitionMock();
        $selector = new TaskSelector($definition);

        $definition->expects($this->any())
                   ->method('hasInvocable')
                   ->will($this->returnCallback(function($taskName) {
                       return ':default' === $taskName ? true : false;
                   }));

        $this->assertEquals(':default', $selector->select(null));
        $this->assertEquals(':default', $selector->select(':default'));
        $this->assertEquals(':default', $selector->select(':the-task-that-wasnt-there'));
    }

    public function testTaskSelection()
    {
        $definition = $this->getDefinitionMock();
        $selector = new TaskSelector($definition);

        $definition->expects($this->any())
                   ->method('hasInvocable')
                   ->will($this->returnValue(true));

        $this->assertEquals(':my-task', $selector->select(':my-task'));
    }

    public function getDefinitionMock()
    {
        return $this->getMockBuilder(self::DEFINITION_CLASS)
                    ->setMethods(['hasInvocable', 'getInvocable', 'addInvocable', 'getDependencies'])
                    ->getMock();
    }
}