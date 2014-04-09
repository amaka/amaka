<?php

use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../src/bootstrap.php';

/**
 *
 */
class FeatureContext extends BehatContext
{
    public function __construct()
    {
        $this->useContext('token_replacement', new TokenReplacementContext());
        $this->useContext('directory_handling', new DirectoryHandlingContext());
    }
}
