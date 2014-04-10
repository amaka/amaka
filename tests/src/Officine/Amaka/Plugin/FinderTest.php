<?php

use Officine\Amaka\Plugin\Finder;

class FinderTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $finder = new Finder();
        $this->assertInstanceOf(
            'Symfony\\Component\\Finder\\Finder',
            $finder()
            );
    }
}
