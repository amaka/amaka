<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

use PHP_Timer as Timer;

/**
 * The AbstractRunner
 *
 */
abstract class AbstractRunner
{
    /**
     * The Buildfile DAG
     *
     * @var Officine\Amaka\AmakaScript\AmakaScript
     */
    private $buildfile;

    /**
     * Duration of the build process microseconds
     *
     * @var int
     */
    private $duration = 0;

    public function __construct(AmakaScript $buildfile)
    {
        $this->buildfile = $buildfile;
    }

    /**
     * Run the invocable, and its prerequisites, by calling the invoke method
     *
     * @param string $start
     */
    public function run($start)
    {
        Timer::start();

        $task = $this->buildfile->get($start);
        $nodes = $this->buildfile->get($start)
                                 ->getAdjacencyList();

        foreach ($nodes as $prerequisite) {
            $this->run($prerequisite);
        }

        $task->invoke();

        $this->duration = Timer::stop();
    }

    public function getDuration()
    {
        return $this->duration;
    }
}
