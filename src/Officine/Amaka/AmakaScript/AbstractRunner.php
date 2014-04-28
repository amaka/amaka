<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

/**
 * The AbstractRunner
 *
 */
abstract class AbstractRunner
{
    /**
     * The AmakaScript DAG
     *
     * @var Officine\Amaka\AmakaScript\AmakaScript
     */
    private $amakaScript;

    public function __construct(AmakaScript $amakaScript)
    {
        $this->amakaScript = $amakaScript;
    }

    /**
     * Run the invocable, and its prerequisites, by calling the invoke method
     *
     * @param string $start
     */
    public function run($targetTask)
    {
        $table = $this->amakaScript->getSymbolTable();
        $invocables = $this->amakaScript->getInvocables();

        if ($invocables->contains($targetTask)) {
            $task = $invocables->get($targetTask);
            foreach ($table->getSymbolsRequiredBy($targetTask) as $prerequisite) {
                $this->run($prerequisite);
            }
            $task->invoke();
        } else {
            throw new \RuntimeException("The named invocable '{$targetTask}' could not be retrieved from the script definition");
        }
    }
}
