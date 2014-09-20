<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use Officine\Amaka\AmakaScript\AmakaScript;

use PHPUnit_Framework_TestCase as TestCase;

class AmakaScriptTest extends TestCase
{
    const DEFINITION_CLASS = 'Officine\\Amaka\\AmakaScript\\Definition\\DefinitionInterface';

    public function testInitialisationScriptsFromArrayDefinition()
    {
        $definition = $this->getMockBuilder(self::DEFINITION_CLASS)
                           ->getMock();

        $script = new AmakaScript($definition);
    }
}
