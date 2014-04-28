<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Officine\Amaka\ErrorReporting\Formatter\ErrorFormatter;

class ErrorReportingContext extends BehatContext
{
    private $errorType;
    private $errorFile;
    private $errorLine;
    private $erororTitle;
    private $errorMessage;
    private $errorResolutions = [];
    private $errorString;

    /**
     * @Given /^this error happens inside of file "([^"]*)" at line "([^"]*)"$/
     */
    public function thisErrorHappensInsideOfFileAtLine($file, $line)
    {
        $this->errorFile = $file;
        $this->errorLine = $line;
    }

    /**
     * @Given /^that "([^"]*)" is encountered$/
     */
    public function thatIsEncountered($title)
    {
        $this->errorTitle = $title;
        $this->errorType = 'error';
    }

    /**
     * @Given /^a fatal error "([^"]*)" is encountered$/
     */
    public function aFatalErrorIsEncountered($title)
    {
        $this->errorTitle = $title;
        $this->errorType = 'failure';
    }

    /**
     * @Given /^this error has a message that says$/
     */
    public function thisErrorHasAMessageThatSays(PyStringNode $message)
    {
        $this->errorMessage = (string) $message;
    }

    /**
     * @When /^the error is printed$/
     */
    public function theErrorIsPrinted()
    {
        $error = $this->buildError();
        $this->errorString = ErrorFormatter::format($error);
    }

    private function buildError()
    {
        $params = [$this->errorTitle, $this->errorMessage, $this->errorResolutions];
        $handle = ['Officine\Amaka\ErrorReporting\Trigger', $this->errorType];

        $trigger = call_user_func_array($handle, $params);
        assertInstanceOf('Officine\Amaka\ErrorReporting\Trigger', $trigger);

        $error = $trigger->setFileName($this->errorFile)
                         ->setFileLine($this->errorLine)
                         ->build();

        assertInstanceOf('Officine\Amaka\ErrorReporting\ErrorInterface', $error);

        return $error;
    }

    /**
     * @Then /^the output on the screen should be$/
     */
    public function theOutputOnTheScreenShouldBe(PyStringNode $expectedOutputString)
    {
        assertEquals((string) $expectedOutputString, $this->errorString);
    }

    /**
     * @When /^this error has a resolution saying "([^"]*)"$/
     */
    public function thisErrorHasAResolutionSaying($resolution)
    {
        $this->errorResolutions[] = $resolution;
    }

    /**
     * @When /^this error has a resolution saying "([^"]*)" that says$/
     */
    public function thisErrorHasAResolutionSayingThatSays($resolution, PyStringNode $message)
    {
        $this->errorResolutions[] = [$resolution => (string) $message];
    }
}
