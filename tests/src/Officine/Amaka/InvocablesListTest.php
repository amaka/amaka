<?php


use Officine\Amaka\InvocablesList;

class InvocablesListTest extends PHPUnit_Framework_TestCase
{
    private $anInvocable;
    private $anotherInvocable;

    public function setUp()
    {
        $this->list = new InvocablesList();
        $this->assertTrue($this->list->isEmpty());

        $builder = $this->getMockBuilder('Officine\Amaka\Invocable')
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
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function addingStringsThrows()
    {
        $this->list->add('string are invalid arguments!');
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
        $this->list->add($this->anInvocable);

        $this->assertSame($this->anInvocable, $this->list->first());
        $this->assertSame($this->anInvocable, $this->list->last());

        $this->assertFalse($this->list->isEmpty());
        $this->assertEquals(1, $this->list->count());
    }

    /**
     * @test
     */
    public function addingTheSameInvocableMultipleTimesIsAnIdempotentOperation()
    {
        $this->list->add($this->anInvocable);
        $this->list->add($this->anInvocable);

        $this->assertSame($this->list->first(), $this->list->last());
        $this->assertEquals(1, $this->list->count());
    }

    /**
     * @test
     */
    public function addingAndRemovingElementsFromTheList()
    {
        $this->list->add($this->anInvocable);
        $this->list->add($this->anotherInvocable);

        $this->assertNotSame($this->list->first(), $this->list->last());

        $this->list->remove($this->anInvocable);

        $this->assertSame($this->list->first(), $this->list->last());
    }

    /**
     * @test
     */
    public function retrievingAnInvocableByObjectInstance()
    {
        $this->list->add($this->anInvocable);
        $this->assertSame($this->anInvocable, $this->list->get($this->anInvocable));
    }

    /**
     * @test
     */
    public function checkingElementsInTheList()
    {
        $this->list->add($this->anInvocable);
        $this->assertTrue($this->list->contains($this->anInvocable));
        $this->assertFalse($this->list->contains($this->anotherInvocable));
    }

    /**
     * @test
     */
    public function elementKey_should_accept_Invocable_and_return_its_name()
    {
        $this->assertSame(
            $this->anInvocable->getName(),
            InvocablesList::elementKey($this->anInvocable)
        );
    }

    /**
     * @test
     */
    public function elementKey_should_accept_string_and_return_it_unchanged()
    {
        $this->assertSame(
            'foo',
            InvocablesList::elementKey('foo')
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elementKey_should_throw_with_booleans()
    {
        InvocablesList::elementKey(true);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elementKey_should_throw_with_arrays()
    {
        InvocablesList::elementKey(array('a', 'b', 'c'));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elementKey_should_throw_with_all_objects()
    {
        InvocablesList::elementKey(new StdClass());
    }

    /**
     * @test
     */
    public function nullIsReturnedWhenAccessingUndefinedElements()
    {
        $this->list->add($this->anInvocable);
        $this->assertNull($this->list->get($this->anotherInvocable));
    }

    /**
     * @test
     */
    public function removingElementsFromTheList()
    {
        $this->list->add($this->anInvocable);
        $this->list->remove($this->anInvocable);

        $this->assertEquals(0, $this->list->count());
        $this->assertFalse($this->list->contains($this->anInvocable));

        $this->list->add($this->anInvocable);
        $this->assertTrue($this->list->contains($this->anInvocable));
    }

    /**
     * @test
     */
    public function invocablesListElementsCanBeIteratedUsingForeach()
    {
        $this->list->add($this->anInvocable);
        $this->list->add($this->anotherInvocable);

        foreach ($this->list as $name => $element) {
            $this->assertInternalType('string', $name);
            $this->assertInstanceOf(
                '\Officine\Amaka\Invocable',
                $element
            );
            $this->assertTrue($this->list->contains($element));
        }
    }
}
