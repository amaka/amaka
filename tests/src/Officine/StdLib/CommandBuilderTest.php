<?php

namespace Officine\Test\StdLib;

use PHPUnit_Framework_TestCase as TestCase;
use Officine\StdLib\CommandBuilder;

class CommandBuilderTet extends TestCase
{
    public function testShouldGenerateCommandString()
    {
        $builder = new CommandBuilder('ls');
        $this->assertEquals('ls', $builder->getCommandString());
    }

    public function testShouldSupportAddingArguments()
    {
        $builder = new CommandBuilder('ls');
        $builder->addArgument('-la');
        $this->assertEquals("ls '-la'", $builder->getCommandString());
    }

    public function testConsecutiveAddMultipleArguments()
    {
        $builder = new CommandBuilder('grep');
        $builder->addArgument('FooBar');
        $builder->addArgument('-R');
        $builder->addArgument('-i');
        $builder->addArgument('src/');
        $this->assertEquals(
            "grep 'FooBar' '-R' '-i' 'src/'",
            $builder->getCommandString()
        );
    }

    public function testShouldEscapeArguments()
    {
        $builder = new CommandBuilder('ls');
        $builder->addArgument('-l');
        $builder->addArgument('src/name with spaces');

        $this->assertEquals(
            "ls '-l' 'src/name with spaces'",
            $builder->getCommandString()
        );
    }

    public function testShouldAcceptArgumentsWithValues()
    {
        $builder = new CommandBuilder('phpunit');
        $builder->addArgument('-c', 'path/to a/phpunit.xml');

        $this->assertEquals(
            "phpunit '-c' 'path/to a/phpunit.xml'",
            $builder->getCommandString()
        );
    }
}
