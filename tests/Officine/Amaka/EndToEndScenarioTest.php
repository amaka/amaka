<?php

use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\Specs\EndToEndScenario;

class EndToEndScenarioTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCollectCliArgumentsInAString()
    {
        $scenario = new EndToEndScenario('bin/amaka');
        $this->assertEquals("bin/amaka", $scenario->getAmakaCommand());

        $scenario->addArgument('--help');
        $this->assertEquals("bin/amaka --help", $scenario->getAmakaCommand());

        $scenario->removeArgument('--help');
        $scenario->removeArgument('--foo'); // fails gracefully

        $this->assertEquals("bin/amaka", $scenario->getAmakaCommand());
    }

    /**
     * @test
     */
    public function shouldSucceedInInvokingATtyOnThisSystem()
    {
        $scenario = new EndToEndScenario("php -r 'exit(0);'");
        $this->assertTrue($scenario->runCommand());

        //
        $scenario = new EndToEndScenario("php -r 'exit(1);'");
        $this->assertFalse($scenario->runCommand());
    }
}
