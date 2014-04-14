<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\AmakaScript\Dag;

/**
 * This class can be used to check the presence of cycles inside an
 * amaka script.
 *
 */
class CycleDetector
{
    private $dag;

    public function __construct(AmakaScript $buildfile)
    {
        $this->dag = new Dag($buildfile);
    }

    /**
     * Check the graph for cycles.
     *
     * If an $entryPoint is given, which must be the name of an invocable
     * contained in the amaka script, then the graph will be checked only
     * from that point onwards. Otherwise the entire graph is checked.
     *
     * @param string $entryPoint
     * @return bool
     */
    public function isValid($entryPoint = null)
    {
        return $this->dag->isValid($entryPoint);
    }
}
