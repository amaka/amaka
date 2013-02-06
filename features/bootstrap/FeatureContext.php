<?php

require_once __DIR__ . '/../../src/bootstrap.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Officine\Amaka\Plugin\TokenReplacement;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $plugin;

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
    public function aFileWith(PyStringNode $content)
    {
        $this->theFile = tempnam(null, null);
        file_put_contents($this->theFile, $content);
    }

    /**
     * @When /^I bind the value "([^"]*)" to the token "([^"]*)"$/
     */
    public function iBindTheValueToTheToken($value, $token)
    {
        $this->plugin->bind($token, $value);
        assertSame($value, $this->plugin->interpret($token));
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
}
