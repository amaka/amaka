<?php

use Officine\Amaka\AmakaScript\SymbolTable;
use Officine\Amaka\AmakaScript\CycleDetector;

class CycleDetectorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->table = new SymbolTable();
    }

    /**
     * @test
     */
    public function emptyTableHasNoCycles()
    {
        $cd = new CycleDetector($this->table);
        $this->assertTrue($cd->isValid());
    }

    /**
     * @test
     */
    public function selfCycleDetection()
    {
        $this->table->addSymbol(':A', ':A');

        $cd = new CycleDetector($this->table);

        $this->assertFalse($cd->isValid(':A'));
        $this->assertFalse($cd->isValid());
    }

    /**
     * @test
     */
    public function otherCyclesDetection()
    {
        $this->table->addSymbol(':A', ':C');
        $this->table->addSymbol(':C', ':A');

        $cd = new CycleDetector($this->table);

        $this->assertFalse($cd->isValid(':A'));
        $this->assertFalse($cd->isValid());
    }
}
