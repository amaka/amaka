<?php

use PHPUnit_Framework_TestCase as TestCase;
use Officine\Amaka\AmakaScript\Definition\ArrayDefinition;

class ArrayDefinitionTest extends TestCase
{
    private $anInvocable;
    private $anotherInvocable;

    public function setUp()
    {
        $this->definition = new ArrayDefinition($this->getMock('Officine\\Amaka\\AmakaScript\\SymbolTable'));
        $this->assertTrue($this->definition->isEmpty());

        $builder = $this->getMockBuilder('Officine\\Amaka\\Invocable')
                        ->setMethods(array('getName', 'invoke', 'hasInvoked'));

        $this->anInvocable = $builder->getMock();
        $this->anotherInvocable = $builder->getMock();

        $this->anInvocable->expects($this->any())
                        ->method('getName')
                        ->will($this->returnValue('A'));

        $this->anotherInvocable->expects($this->any())
                             ->method('getName')
                             ->will($this->returnValue('B'));
    }

    /**
     * We're adding a mock Invocable to the list and checking
     * that it's correctly added into the collection.
     *
     * We also check that query method such as isEmpty and count
     * return expected values.
     *
     * @test
     */
    public function addingAndRetrievingAnInvocableElement()
    {
        $this->definition->addInvocable($this->anInvocable);

        $this->assertSame($this->anInvocable, $this->definition->first());
        $this->assertSame($this->anInvocable, $this->definition->last());

        $this->assertFalse($this->definition->isEmpty());
        $this->assertCount(1, $this->definition);
    }

    /**
     * @test
     */
    public function addingTheSameInvocableMultipleTimesIsAnIdempotentOperation()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->definition->addInvocable($this->anInvocable);

        $this->assertSame($this->definition->first(), $this->definition->last());
        $this->assertCount(1, $this->definition);
    }

    /**
     * @test
     */
    public function addingAndRemovingElementsFromTheList()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->definition->addInvocable($this->anotherInvocable);

        $this->assertNotSame($this->definition->first(), $this->definition->last());

        $this->definition->removeInvocable($this->anInvocable);

        $this->assertSame($this->definition->first(), $this->definition->last());
    }

    /**
     * @test
     */
    public function retrievingAnInvocableByObjectInstance()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->assertSame($this->anInvocable, $this->definition->getInvocable($this->anInvocable));
    }

    /**
     * @test
     */
    public function checkingElementsInTheList()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->assertTrue($this->definition->hasInvocable($this->anInvocable));
        $this->assertFalse($this->definition->hasInvocable($this->anotherInvocable));
    }

    /**
     * @test
     */
    public function elementKey_should_accept_Invocable_and_return_its_name()
    {
        $this->assertSame(
            $this->anInvocable->getName(),
            ArrayDefinition::elementKey($this->anInvocable)
        );
    }

    /**
     * @test
     */
    public function elementKey_should_accept_string_and_return_it_unchanged()
    {
        $this->assertSame(
            'foo',
            ArrayDefinition::elementKey('foo')
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elementKey_should_throw_with_booleans()
    {
        ArrayDefinition::elementKey(true);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elementKey_should_throw_with_arrays()
    {
        ArrayDefinition::elementKey(['a', 'b', 'c']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elementKey_should_throw_with_all_objects()
    {
        ArrayDefinition::elementKey(new StdClass());
    }

    /**
     * @test
     */
    public function nullIsReturnedWhenAccessingUndefinedElements()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->assertNull($this->definition->getInvocable($this->anotherInvocable));
    }

    /**
     * @test
     */
    public function removingElementsFromTheList()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->definition->removeInvocable($this->anInvocable);

        $this->assertCount(0, $this->definition);
        $this->assertFalse($this->definition->hasInvocable($this->anInvocable));

        $this->definition->addInvocable($this->anInvocable);
        $this->assertTrue($this->definition->hasInvocable($this->anInvocable));
    }

    /**
     * @test
     */
    public function invocablesListElementsCanBeIteratedUsingForeach()
    {
        $this->definition->addInvocable($this->anInvocable);
        $this->definition->addInvocable($this->anotherInvocable);

        foreach ($this->definition as $name => $element) {
            $this->assertInternalType('string', $name);
            $this->assertInstanceOf(
                '\Officine\Amaka\Invocable',
                $element
            );
            $this->assertTrue($this->definition->hasInvocable($element));
        }
    }
}
