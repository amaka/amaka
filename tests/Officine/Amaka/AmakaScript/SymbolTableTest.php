<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
use PHPUnit_Framework_TestCase as TestCase;

use Officine\Amaka\AmakaScript\SymbolTable;

class SymbolTableTest extends TestCase
{
    public function testAddingAndRetrievingASymbol()
    {
        $table = new SymbolTable();
        $table->addSymbol('a');

        $this->assertTrue($table->hasSymbol('a'));
        $this->assertEmpty($table->getSymbolsRequiredBy('a'));
    }

    public function testAddingASymbolWithRequisites()
    {
        $table = new SymbolTable();
        $table->addSymbol('a', ['b', 'c', 'd']);

        $this->assertTrue($table->hasSymbol('a'));
        $this->assertTrue($table->hasSymbol('b'));
        $this->assertTrue($table->hasSymbol('c'));
        $this->assertTrue($table->hasSymbol('d'));

        $this->assertEquals(['b', 'c', 'd'], $table->getSymbolsRequiredBy('a'));
    }

    public function testAddingMultipleSymbols()
    {
        $table = new SymbolTable();
        $table->addSymbol('a');
        $table->addSymbol('b');

        $this->assertEquals(
            [
                'a' => [],
                'b' => []
            ],
            $table->getAllSymbols()
        );
    }

    public function testAddingMultipleSymbolsWithRequisites()
    {
        $table = new SymbolTable();
        $table->addSymbol('a', ['c']);
        $table->addSymbol('b');
        $table->addSymbol('c');

        $table->addRequisiteToSymbol('b', 'd');
        $table->addRequisiteToSymbol('b', 'c');
        $table->addRequisiteToSymbol('a', 'd');

        $table->addSymbol('d', 'e');
        $table->addSymbol('e', ['f', 'g']);

        $this->assertEquals(
            [
                'a' => ['c', 'd'],
                'b' => ['d', 'c'],
                'c' => [],
                'd' => ['e'],
                'e' => ['f', 'g'],
                'f' => [],
                'g' => [],
            ],
            $table->getAllSymbols()
        );
    }

    public function testMergingTableBIntoA()
    {
        $tableA = new SymbolTable();
        $tableB = new SymbolTable();

        $tableA->addSymbol('a', 'b');
        $tableB->addSymbol('c', 'd');
        $tableB->addSymbol('a', 'c');

        $this->assertEquals(
            [
                'a' => ['b', 'c'],
                'b' => [],
                'c' => ['d'],
                'd' => [],
            ],
            $tableA->mergeWith($tableB)->getAllSymbols()
        );
    }

    public function testMergingTableAIntoB()
    {
        $tableA = new SymbolTable();
        $tableB = new SymbolTable();

        $tableA->addSymbol('a', 'b');
        $tableB->addSymbol('c', 'd');
        $tableB->addSymbol('a', 'c');

        $this->assertEquals(
            [
                'c' => ['d'],
                'b' => [],
                'a' => ['c', 'b'],
                'd' => [],
            ],
            $tableB->mergeWith($tableA)->getAllSymbols()
        );
    }
}
