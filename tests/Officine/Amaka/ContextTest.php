<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use Officine\Amaka\Context;

class ContextTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->context = new Context();
    }

    /**
     * @test
     */
    public function defalt_working_directory_should_be_null()
    {
        $this->assertNull($this->context->getWorkingDirectory());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setting_relative_working_directory_should_throw()
    {
        $this->context->setWorkingDirectory('relative/path/to/foo');
    }

    /**
     * @test
     */
    public function should_allow_setting_absolute_working_directory()
    {
        $this->context->setWorkingDirectory('/absolute/path/to/foo');
        $this->assertSame('/absolute/path/to/foo', $this->context->getWorkingDirectory());
    }

    /**
     * @test
     */
    public function should_have_getParam_method_accepting_parameter_key()
    {
        $this->context->getParam('key');
    }

    /**
     * @test
     */
    public function new_context_should_have_empty_params()
    {
        $this->assertNotNull($this->context->getParams());
        $this->assertEmpty($this->context->getParams());
    }

    /**
     * @test
     */
    public function should_return_null_when_accessing_non_existing_element()
    {
        $this->assertNull($this->context->getParam('bogus'));
    }

    /**
     * @test
     */
    public function calls_to_setParams_with_empty_array_should_be_ignored()
    {
        $this->context->setParams(array('a' => 1));
        $this->context->setParams(array());
        $this->context->setParams(array('b' => 1));

        $this->assertEquals(array('a' => 1, 'b' => 1), $this->context->getParams());
    }

    /**
     * @test
     */
    public function should_have_setParam_method_to_set_parameters_value_by_key()
    {
        $this->context->setParam('key', 'value');
        $this->assertSame('value', $this->context->getParam('key'));
    }

    /**
     * @test
     */
    public function setParams_should_allow_parameters_to_be_set_at_once()
    {
        $this->context->setParams(array('key0' => 'value', 'key1' => 'value'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setParams_should_throw_when_argument_is_not_an_hash_table()
    {
        $this->context->setParams(array(0, 'key1'));
    }

    /**
     * @test
     */
    public function setParams_should_merge_to_the_existing_ones_and_not_reset()
    {
        $this->context->setParams(array('key0' => 'value'));
        $this->context->setParams(array('key1' => 'value'));

        $this->assertEquals(
            array('key0' => 'value', 'key1' => 'value'),
            $this->context->getParams()
        );

        $this->assertSame('value', $this->context->getParam('key0'));
        $this->assertSame('value', $this->context->getParam('key1'));
    }
}
