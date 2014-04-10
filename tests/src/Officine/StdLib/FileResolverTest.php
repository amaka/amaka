<?php

use Officine\StdLib\FileResolver;

class FileResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function resolving_without_declaring_search_paths_first_should_throw()
    {
        $resolver = new FileResolver();
        $resolver->resolve('foo');
    }

    /**
     * @test
     * @expectedExcepion \IllegalArgumentException
     */
    public function trying_to_resolve_empty_filename_should_throw()
    {
        $resolver = new FileResolver();
        $resolver->addPath('/foo/bar');
        $resolver->resolve('');
    }

    /**
     * @test
     */
    public function addPath_should_provide_fluent_interface()
    {
        $resolver = new FileResolver();
        $this->assertSame($resolver, $resolver->addPath('/foo/bar'));
    }

    /**
     * @test
     */
    public function resolve_should_return_true_and_fire_callback_when_file_is_found()
    {
        $resolver = new FileResolver();
        $resolver->addPath(__DIR__);

        $callback = $this->getMock('stdClass', array('call'));
        $callback->expects($this->once())
                 ->method('call');

        $this->assertTrue($resolver->resolve('FileResolverTest.php', array($callback, 'call')));
    }

    /**
     * @test
     */
    public function resolve_should_return_false_and_fire_errback_when_file_is_not_found()
    {
        $resolver = new FileResolver();
        $resolver->addPath(__DIR__);

        $errback = $this->getMock('stdClass', array('call'));
        $errback->expects($this->once())
                ->method('call');

        $this->assertFalse($resolver->resolve('bogus', null, array($errback, 'call')));
    }
}
