<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

require_once 'PHPUnit/Framework/Assert/Functions.php';

use Officine\Amaka\Plugin\Directories;

/**
 *
 */
class DirectoryHandlingContext extends BehatContext
{
    private $plugin;

    public function __construct()
    {
        $this->plugin = new Directories();
    }

    /**
     * @Given /^an instance of the plugin in "([^"]*)"$/
     */
    public function anInstanceOfThePluginIn($workingDirectory)
    {
        $this->plugin->setWorkingDirectory($workingDirectory);
    }

    /**
     * @Then /^the working directory should be "([^"]*)"$/
     */
    public function theWorkingDirectoryShouldBe($directory)
    {
        assertEquals($directory, $this->plugin->getWorkingDirectory());
    }

    /**
     * @Given /^calling the "([^"]*)" should throw$/
     */
    public function callingTheShouldThrow($method)
    {
        try {
            call_user_func(array($this->plugin, $method));
        } catch (\BadMethodCallException $e) {
            return true;
        }
        throw new Exception("The exception didn't happen.");
    }

    /**
     * @When /^the "([^"]*)" method is called with "([^"]*)"$/
     */
    public function theMethodIsCalledWith($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^the directory "([^"]*)" should exist$/
     */
    public function theDirectoryShouldExist($arg1)
    {
        throw new PendingException();
    }
}
