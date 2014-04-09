<?php

use Behat\Behat\Context\BehatContext;

class ProvaContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        echo __CLASS__ . ' initialized' . PHP_EOL;
    }

    /**
     * @Given /^an initial condition$/
     */
    public function anInitialCondition()
    {
        throw new PendingException();
    }

    /**
     * @Then /^something happens$/
     */
    public function somethingHappens()
    {
        throw new PendingException();
    }
}