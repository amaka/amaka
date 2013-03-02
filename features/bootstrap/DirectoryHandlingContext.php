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
        $plugin = new Directories();
        $workingDirectory = sys_get_temp_dir() . '/amaka-tests';

        chdir(sys_get_temp_dir());
        if (! file_exists($workingDirectory)) {
            mkdir($workingDirectory);
        }
        $plugin->setWorkingDirectory($workingDirectory);

        $this->plugin = $plugin;
    }

    /**
     * @Given /^the test script is run in the system temporary directory$/
     */
    public function theTestScriptIsRunInTheSystemTemporaryDirectory()
    {
        assertEquals(sys_get_temp_dir(), getcwd());
    }

    /**
     * @Given /^the directory "([^"]*)" is the plugin working directory$/
     */
    public function theDirectoryIsThePluginWorkingDirectory($directory)
    {
        assertEquals(basename($this->plugin->getWorkingDirectory()), $directory);
    }

    /**
     * @Given /^the directory "([^"]*)" doesn\'t exist$/
     */
    public function theDirectoryDoesnTExist($directory)
    {
        assertFalse(file_exists($directory),
                    "Failed asserting the directory '{$directory}' doesn't exist.");
    }

    /**
     * @Given /^the directory "([^"]*)" exists$/
     */
    public function theDirectoryExists($directory)
    {
        assertTrue(file_exists($directory),
                    "Failed asserting the directory '{$directory}' does exist.");
    }

    /**
     * @Then /^the directory "([^"]*)" is created$/
     */
    public function theDirectoryCreated($directory)
    {
        assertTrue($this->plugin->exists($directory));
    }

    /**
     * @Then /^the directory "([^"]*)" is removed/
     */
    public function theDirectoryRemoved($directory)
    {
        assertFalse($this->plugin->exists($directory));
    }


    /**
     * @When /^the developer calls the "([^"]*)" method with "([^"]*)"$/
     */
    public function theDeveloperCallsTheMethodWith($method, $arg)
    {
        call_user_func(array($this->plugin, $method), $arg);
    }

    /**
     * @When /^the developer calls the "([^"]*)" method with "([^"]*)" and "([^"]*)"$/
     */
    public function theDeveloperCallsTheMethodWithAnd($method, $first, $second)
    {
        call_user_func_array(array($this->plugin, $method), array($first, $second));
    }
}
