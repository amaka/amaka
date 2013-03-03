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
        $wd = sys_get_temp_dir() . '/amaka-tests';
        $plugin = new Directories($wd);

        chdir(sys_get_temp_dir());
        if (! file_exists($wd)) {
            mkdir($wd);
        }

        $this->plugin = $plugin;
    }

    /**
     * @AfterScenario
     */
    public function cleanup()
    {
        $d = new Directories(sys_get_temp_dir());
        if ($d->exists('amaka-tests')) {
            $d->remove('amaka-tests');
        }
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
        assertFalse($this->plugin->exists($directory),
                    "Failed asserting the directory '{$directory}' doesn't exist.");
    }

    /**
     * @Given /^the directory "([^"]*)" exists$/
     */
    public function theDirectoryExists($directory)
    {
        assertTrue($this->plugin->exists($directory),
                    "Failed asserting the directory '{$directory}' exists.");
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

    /**
     * @Given /^the directory "([^"]*)" exists in the system temp directory$/
     */
    public function theDirectoryExistsInTheSystemTempDirectory($dir)
    {
        $temp = sys_get_temp_dir();
        assertTrue(file_exists($temp . '/' . $dir));
    }
}
