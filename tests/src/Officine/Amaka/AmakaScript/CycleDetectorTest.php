<?php

use Officine\Amaka\AmakaScript\AmakaScript;
use Officine\Amaka\AmakaScript\CycleDetector;

class CycleDetectorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->amakaScript      = new AmakaScript();
        $this->loopAmakaScript  = new AmakaScript();
        $this->emptyAmakaScript = new AmakaScript();
        $this->cycleAmakaScript = new AmakaScript();
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function creating_one_without_a_buildfile_should_not_be_allowed()
    {
        new CycleDetector();
    }

    /**
     * @test
     */
    public function should_not_detect_any_cycles_in_empty_buildfile()
    {
        $cd = new CycleDetector($this->emptyAmakaScript);
        $this->assertTrue($cd->isValid());
    }

    /**
     * @test
     */
    public function should_detect_self_loops()
    {
        // construct the simple loop buildfile
        $loop = $this->loopAmakaScript->task(':A')
                                    ->dependsOn(':A');
        $this->loopAmakaScript->add($loop);

        $cd = new CycleDetector($this->loopAmakaScript);
        $this->assertFalse($cd->isValid());
    }

    /**
     * @test
     */
    public function should_detect_simple_cycles()
    {
        // construct the simple cycle buildfile
        $cycleA = $this->cycleAmakaScript->task(':A')
                                       ->dependsOn(':B');

        $cycleB = $this->cycleAmakaScript->task(':B')
                                       ->dependsOn(':A');

        $this->cycleAmakaScript->add($cycleA)
                             ->add($cycleB);

        $cd = new CycleDetector($this->cycleAmakaScript);
        $this->assertFalse($cd->isValid());
    }

    /**
     * @test
     */
    public function should_not_trigger_node_class_loading()
    {
        $task = $this->amakaScript->task(':A')
                                ->dependsOn('Composer');

        $this->amakaScript->add($task);

        $cd = new CycleDetector($this->amakaScript);
        $cd->isValid(':A');
    }
}
