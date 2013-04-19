<?php

use Officine\Amaka\Context;
use Officine\Amaka\Amaka;

class AmakaScriptLoadingLoadingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException Officine\Amaka\AmakaScript\AmakaScriptNotFoundException
     */
    public function loading_non_existing_amaka_script_should_throw()
    {
        $amaka = new Amaka();
        $amaka->loadAmakaScript('foo');
    }

    /**
     * @test
     */
    public function amaka_script_loading_relies_on_Context_working_directory()
    {
        $context = new Context();
        $context->setWorkingDirectory(__DIR__ . '/AmakaScript/_files');

        $amaka = new Amaka($context);
        $amaka->loadAmakaScript('Amkfile');
    }

    /**
     * @test
     */
    public function retrieving_the_amaka_script_before_loading_should_yield_null()
    {
        $amaka = new Amaka();
        $this->assertNull($amaka->getAmakaScript());
    }
}
