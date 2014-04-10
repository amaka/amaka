<?php

use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\Specs\EndToEndSpecContext;

class EndToEndSpecContextTest extends TestCase
{
    private $commandKey;
    private $contextDirectory;
    private $contextDirectoryKey;

    public function setUp()
    {
        $this->commandKey = EndToEndSpecContext::AMAKA_CMD_KEY;
        $this->contextDirectory = __DIR__ . '/_files';
        $this->contextDirectoryKey = EndToEndSpecContext::CONTEXT_DIR_KEY;
    }

    /**
     * @test
     */
    public function shouldCollectCliArgumentsInAString()
    {
        $scenario = new EndToEndSpecContext([$this->commandKey => 'bin/amaka']);
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
        $failureMessage = "Failed to invoke PHP executable using 'system'. Is the 'php' binary in the path?";

        $scenario = new EndToEndSpecContext([$this->commandKey => "php -r 'exit(0);'"]);
        $this->assertTrue($scenario->runCommand(), $failureMessage);

        $scenario = new EndToEndSpecContext([$this->commandKey => "php -r 'exit(1);'"]);
        $this->assertFalse($scenario->runCommand(), $failureMessage);
    }

    /**
     * @test
     */
    public function shouldImplementBehatContextAccessMethods()
    {
        $scenario = new EndToEndSpecContext();
        $this->assertInstanceOf('Behat\Behat\Context\ContextInterface', $scenario);

        $this->assertEmpty($scenario->getSubcontexts());

        $scenario->addSubcontext($scenario);

        $this->assertNotEmpty($scenario->getSubcontexts());
        $this->assertContains($scenario, $scenario->getSubcontexts());

        $this->assertSame($scenario, $scenario->getSubcontextByClassName('Officine\Amaka\Specs\EndToEndSpecContext'));
    }

    /**
     * @test
     */
    public function shouldLoadContextClassesFromDirectory()
    {
        $scenario = new EndToEndSpecContext([$this->contextDirectoryKey => $this->contextDirectory]);

        $this->assertFileExists($this->contextDirectory);
        $this->assertFileExists($this->contextDirectory . '/ExampleContext.php');
        $scenario->tryLoadingContextClasses();

        $context = $scenario->getSubcontextByClassName('Example\ExampleContext');
        $this->assertNotNull($context);
        $this->assertContains($context, $scenario->getSubcontexts());
    }
}
