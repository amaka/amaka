<?php

require_once 'PHPUnit/Framework/Assert/Functions.php';

use Behat\Behat\Event\SuiteEvent;
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

    /**
     * Collection of files used for the scope of this specification
     */
    private $tempFiles = array();

    private $theFile;
    private $theDestinationFile;

    private static $baseDir;

    public function __construct()
    {
        $this->plugin = new TokenReplacement();
    }

    /**
     * Create a file which will be used for this specification
     */
    public function getAFile($filename = null)
    {
        if (null === $filename) {
            $filepath = tempnam(self::$baseDir, null);
            $filename = basename($filepath);
        } else {
            $filepath = self::$baseDir . '/' . basename($filename);
            touch($filename);
        }

        $this->tempFiles[$filename] = $filepath;

        return $filepath;
    }

    public function getFileContent($filename = null)
    {
        if (null === $filename) {
            return file_get_contents($this->theFile);
        }
        return file_get_contents($this->tempFiles[$filename]);
    }

    /**
     * @Given /^an empty file$/
     */
    public function anEmptyFile()
    {
        $this->theFile = $this->getAFile();
    }


    /**
     * @When /^the value "([^"]*)" is bound to the token "([^"]*)"$/
     */
    public function bindValueToToken($value, $token)
    {
        $this->plugin->bind($token, $value);
        assertEquals($value, $this->plugin->interpret($token));
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
        assertEmpty($this->getFileContent());
    }

    /**
     * @Given /^a file with the following content$/
     */
    public function aFileContaining(PyStringNode $content)
    {
        $this->theFile = $this->getAFile();
        file_put_contents($this->theFile, $content);
    }

    /**
     * @Given /^a file called "([^"]*)" with the following content$/
     */
    public function aFileCalledWithTheFollowingContent($filename, PyStringNode $content)
    {
        $file = $this->getAFile($filename);
        file_put_contents($file, $content);
    }

    /**
     * @When /^passing the Finder "([^"]*)" to the replacement plugin$/
     */
    public function passingTheFinderToTheReplacementPlugin($finderPattern)
    {
        $finder  = new Officine\Amaka\Plugin\Finder();
        $fileset = $finder->files()->name($finderPattern)->in(self::$baseDir);

        $this->plugin->replaceInto($fileset);
    }

    /**
     * @Then /^the file should contain$/
     */
    public function theFileShouldContain(PyStringNode $content)
    {
        assertEquals((string) $content, $this->getFileContent());
    }

    /**
     * @Then /^the file "([^"]*)" should contain$/
     */
    public function theFileShouldContain2($filename, PyStringNode $content)
    {
        assertEquals((string) $content, $this->getFileContent($filename));
    }

    /**
     * @Given /^the file should not contain the original token "([^"]*)"$/
     */
    public function theFileShouldNotContainTheOriginalToken($token)
    {
        assertNotContains($token, $this->getFileContent());
    }

    /**
     * @When /^the token replacement plugin is run$/
     */
    public function theTokenReplacementPluginIsRun()
    {
        $this->theDestinationFile = $this->getAFile();
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
     * @When /^passing the SplFileInfo object to token replacement plugin$/
     */
    public function passingTheSplfileinfoObjectToTokenReplacementPlugin()
    {
        $file = new \SplFileInfo($this->theFile);
        $this->plugin->replaceInto($file);
    }

    /**
     * @AfterScenario
     */
    public function scenarioCleanup()
    {
        foreach (array_merge($this->tempFiles, array($this->theFile, $this->theDestinationFile)) as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @BeforeSuite
     */
    public static function suitePrepare(SuiteEvent $event)
    {
        self::$baseDir = sys_get_temp_dir() . '/' . uniqid();
        if (! is_dir(self::$baseDir)) {
            mkdir(self::$baseDir);
        }
        chdir(self::$baseDir);
    }

    /**
     * @AfterSuite
     */
    public static function suiteCleanup(SuiteEvent $event)
    {
        chdir(sys_get_temp_dir());
        rmdir(self::$baseDir);
    }
}