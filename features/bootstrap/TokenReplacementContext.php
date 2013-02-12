<?php

require_once 'PHPUnit/Framework/Assert/Functions.php';

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Officine\Amaka\Plugin\TokenReplacement;

class TokenReplacementContext extends BehatContext
{
    private $plugin;
    private $theFile;
    private $theDestinationFile;

    public function __construct()
    {
        $this->plugin = new TokenReplacement();
    }

    /**
     * @Given /^an empty file$/
     */
    public function anEmptyFile()
    {
        $this->theFile = tempnam(null, null);
    }


    /**
     * @When /^the value "([^"]*)" is bound to the token "([^"]*)"$/
     */
    public function bindValueToToken($value, $token)
    {
        $this->plugin->bind($token, $value);
        assertSame($value, $this->plugin->interpret($token));
    }

    /**
     * @When /^I run the token replacement plugin$/
     */
    public function iRunTheTokenReplacementPlugin()
    {
        $this->plugin->replaceInto($this->theFile);
    }

    /**
     * @Then /^the file should still be empty$/
     */
    public function theFileShouldStillBeEmpty()
    {
        assertEmpty(file_get_contents($this->theFile));
    }

    /**
     * @Given /^a file with the following content$/
     */
    public function aFileContaining(PyStringNode $content)
    {
        $this->theFile = tempnam(null, null);
        file_put_contents($this->theFile, $content);
    }

    /**
     * @Then /^the file should contain$/
     */
    public function theFileShouldContain(PyStringNode $string)
    {
        assertEquals((string) $string, file_get_contents($this->theFile));
    }

    /**
     * @Given /^the file should not contain the original token "([^"]*)"$/
     */
    public function theFileShouldNotContainTheOriginalToken($token)
    {
        $content = file_get_contents($this->theFile);
        assertNotContains($token, $content);
    }

    /**
     * @When /^the token replacement plugin is run$/
     */
    public function theTokenReplacementPluginIsRun()
    {
        $this->theDestinationFile = tempnam(null, null);
        $this->plugin->replaceFromInto($this->theFile, $this->theDestinationFile);
    }

    /**
     * @Then /^the target file should be created$/
     */
    public function theTargetFileShouldBeCreated()
    {
        assertTrue(file_exists($this->theDestinationFile));
    }

    /**
     * @Given /^the destination file should contain the replaced tokens$/
     */
    public function theDestinationFileShouldContainTheReplacedTokens($content)
    {
        assertEquals((string) $content, file_get_contents($this->theDestinationFile));
    }

    /**
     * @AfterScenario
     */
    public function cleanup()
    {
        foreach (array($this->theFile, $this->theDestinationFile) as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}