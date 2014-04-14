<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use PHPUnit_Framework_TestCase as TestCase;
use Officine\Amaka\Task\Task;

class TaskTest extends TestCase
{
    /**
     * @test
     */
    public function tasks_should_be_Invocable()
    {
        $this->assertInstanceOf(
            'Officine\Amaka\Invocable',
            new Task(':foo')
        );
    }
}
