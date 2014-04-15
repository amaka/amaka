<?php

use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Officine\Amaka\Specs\EndToEndSpecContext;

require_once 'PHPUnit/Framework/Assert/Functions.php';

class ScriptAndTaskLoadingContext extends EndToEndSpecContext
{
    private $scriptsCreated = [];

    /**
     * @Given /^amaka executable is in "([^"]*)"$/
     */
    public function amakaExecutableIsIn($pathToAmaka)
    {
        $path = realpath(__DIR__ . '/../../') . '/' . $pathToAmaka;
        assertFileExists($path);
        $this->setAmakaCommand($path);
    }

    /**
     * @Given /^I run amaka with arguments "([^"]*)"$/
     */
    public function iRunAmakaWithArguments($arguments)
    {
        $args = explode(',', $arguments);
        foreach ($args as $arg) {
            $this->addArgument($arg);
        }
        $this->runCommand();
    }

    /**
     * @Then /^the output on the screen should contain "([^"]*)"$/
     */
    public function theOutputOnTheScreenShouldContain($needle)
    {
        $haystack = $this->getCollectedOutput();
        if ("" == $needle) {
            assertEquals($needle, $haystack);
        } else {
            assertContains($needle, $haystack);
        }
    }

    /**
     * @Then /^the output on the screen should not contain "([^"]*)"$/
     */
    public function theOutputOnTheScreenShouldNotContain($needle)
    {
        $haystack = $this->getCollectedOutput();
        assertNotContains($needle, $haystack);
    }

    /**
     * @Then /^the output on the screen should match "([^"]*)"$/
     */
    public function theOutputOnTheScreenShouldMatch($needle)
    {
        $haystack = $this->getCollectedOutput();

        if ("" == $needle) {
            assertEquals($needle, $haystack);
        } else {
            assertRegexp($needle, $haystack);
        }
    }

    /**
     * @Given /^the program exit status should be non-zero$/
     */
    public function theProgramExitStatusShouldBeNonZero()
    {
        assertNotEquals(0, $this->getLastExitStatus());
    }

    /**
     * @Given /^the program exit status should be zero$/
     */
    public function theProgramExitStatusShouldBeZero()
    {
        assertEquals(0, $this->getLastExitStatus());
    }

    /**
     * @Given /^the current working directory is "([^"]*)"$/
     */
    public function theCurrentWorkingDirectoryIs($directory)
    {
        $sPath = str_replace('%system.temp%', sys_get_temp_dir(), $directory);
        $aPath = realpath($sPath);
        if (! $aPath) {
            throw new Exception("Could not run E2E test: The given path to the current working directory is invalid.");
        }
        chdir($aPath);
        $this->setWorkingDirectory($aPath);
    }

    /**
     * @Given /^the amaka script "([^"]*)" contains$/
     */
    public function thereIsTheAmakaScriptInWith($fileName, PyStringNode $content)
    {
        $this->createAmakaScriptWithContent($fileName, $content);
    }

    /**
     * @Given /^the amaka script "([^"]*)" is available$/
     */
    public function theAmakaScriptIsAvailable($fileName)
    {
        $this->createAmakaScriptWithContent($fileName, "<?php return [];");
    }

    /**
     * @Given /^the amaka script "([^"]*)" is not available$/
     */
    public function theAmakaScriptIsNotAvailable($fileName)
    {
        if ($this->getWorkingDirectory()) {
            $fileName = $this->getWorkingDirectory() . '/' . $fileName;
        }

        assertFileNotExists($fileName);
    }

    /** @AfterScenario */
    public function cleanUnnecessaryAmkfiles()
    {
        foreach ($this->scriptsCreated as $script) {
            unlink($script);
        }
    }

    private function createAmakaScriptWithContent($fileName, $content)
    {
        if ($this->getWorkingDirectory()) {
            $fileName = $this->getWorkingDirectory() . '/' . $fileName;
        }

        file_put_contents($fileName, $content);

        assertFileExists($fileName);
        assertEquals(file_get_contents($fileName), $content);

        $this->scriptsCreated[] = $fileName;
    }
}
