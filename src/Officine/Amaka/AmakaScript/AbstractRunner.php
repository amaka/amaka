<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\AmakaScript\Definition\DefinitionInterface;

/**
 * The AbstractRunner
 *
 */
abstract class AbstractRunner
{
    /**
     * @var Officine\Amaka\AmakaScript\Definition\ArrayDefinition
     */
    private $scriptDefinition;

    public function __construct(DefinitionInterface $scriptDefinition)
    {
        $this->scriptDefinition = $scriptDefinition;
    }

    /**
     * Run the invocable, and its prerequisites, by calling the invoke method
     *
     * @param string $start
     */
    public function run($targetTask)
    {
        if ($this->scriptDefinition->hasInvocable($targetTask)) {
            $task = $this->scriptDefinition->getInvocable($targetTask);
            foreach ($this->scriptDefinition->getDependencies($targetTask) as $prerequisite) {
                $this->run($prerequisite);
            }
            $task->invoke();
        } else {
            throw new \RuntimeException("The named invocable '{$targetTask}' could not be retrieved from the script definition");
        }
    }
}
