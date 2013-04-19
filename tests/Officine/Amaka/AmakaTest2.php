<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Ois\Tests\Amaka;

use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\Amaka;
use Officine\Amaka\Context;

/**
 * The Amaka class is a facade with a simple API to setup and
 * use an instance of Amaka programmatically.
 *
 * @group     amaka
 * @licese    http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaTest extends TestCase
{
    public function setUp()
    {
        $testContext = new Context();
        $testContext->setWorkingDirectory(__DIR__ . '/AmakaScript/_files');

        $this->amaka = new Amaka($testContext);
        $this->amaka->loadBuildfile('Amkfile');
    }
}
