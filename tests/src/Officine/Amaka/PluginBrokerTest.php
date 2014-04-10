<?php

use Officine\Amaka\PluginBroker;
use Officine\Amaka\PluginInterface;

class PluginBrokerTest extends PHPUnit_Framework_TestCase implements PluginInterface
{
    /**
     * @test
     */
    public function register_plugin()
    {
        $broker = new PluginBroker();
        $this->assertTrue($broker->isEmpty());
        $this->assertFalse($broker->contains($this));

        $broker->registerPlugin($this);

        $this->assertFalse($broker->isEmpty());
        $this->assertTrue($broker->contains($this));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function registering_same_plugin_twice_should_throw()
    {
        $broker = new PluginBroker();

        $broker->registerPlugin($this);
        $broker->registerPlugin($this);
    }

    /**
     * @test
     */
    public function retrieving_a_plugin_from_the_broker()
    {
        $broker = new PluginBroker();
        $broker->registerPlugin($this);

        $this->assertSame($this, $broker->plugin('pluginBrokerTest'));
    }

    /**
     * @test
     */
    public function plugins_can_be_unregistered()
    {
        $broker = new PluginBroker();
        $broker->registerPlugin($this);
        $broker->unregisterPlugin($this);

        $this->assertFalse($broker->contains($this));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function retrieving_unregistered_plugin_should_throw()
    {
        $broker = new PluginBroker();
        $broker->plugin('wuauha');
    }

    /**
     * @test
     */
    public function mapping_different_names_to_key()
    {
        $this->assertSame('pluginBrokerTest', PluginBroker::pluginToKey($this));

        $this->assertSame('foo', PluginBroker::pluginToKey('foo'));
        $this->assertSame('foo', PluginBroker::pluginToKey('Foo'));
        $this->assertSame('fOO', PluginBroker::pluginToKey('FOO'));
        $this->assertSame('baz', PluginBroker::pluginToKey('\\Foo\\Bar\\Baz'));
    }
}
