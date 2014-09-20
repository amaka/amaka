<?php

use Officine\Amaka\ErrorReporting\ResolutionTrait;
use PHPUnit_Framework_TestCase as TestCase;

class ResolutionTraitTest extends TestCase
{
    use ResolutionTrait;

    public function testAddingStringResolutions()
    {

        $resolutionName = 'Simple string resolution';
        $this->addResolution($resolutionName);

        $resolutions = $this->getResolutions();
        $this->assertNotEmpty($resolutions);
        $this->assertSame($resolutionName, $resolutions[0]->getName());
    }

    public function testAddingResolutionWithDescriptionAsArrayKeyValue()
    {
        $resolutionName = 'Simple resolution name';
        $resolutionDescription = 'And a description';
        $this->addResolution([$resolutionName => $resolutionDescription]);

        $resolutions = $this->getResolutions();
        $this->assertSame($resolutionName, $resolutions[0]->getName());
        $this->assertSame($resolutionDescription, $resolutions[0]->getDescription());
    }

    public function testAddingResolutionWithDescriptionAsArrayPair()
    {
        $resolutionName = 'Simple resolution name';
        $resolutionDescription = 'And a description';
        $this->addResolution([$resolutionName, $resolutionDescription]);

        $resolutions = $this->getResolutions();
        $this->assertSame($resolutionName, $resolutions[0]->getName());
        $this->assertSame($resolutionDescription, $resolutions[0]->getDescription());
    }

    public function testAddingMultipleResolutionsWithMixedArrayStyles()
    {
        $testResolutions = [
            'Foo',
            ['Bar' => 'raB'],
            ['Baz', 'zaB']
        ];
        $this->addResolutions($testResolutions);

        $actualResolutions = $this->getResolutions();

        $fooRes = reset($actualResolutions);
        $barRes = next($actualResolutions);
        $bazRes = next($actualResolutions);

        $this->assertEquals('Foo', $fooRes->getName());
        $this->assertEquals('Bar', $barRes->getName());
        $this->assertEquals('raB', $barRes->getDescription());

        $this->assertEquals('Baz', $bazRes->getName());
        $this->assertEquals('zaB', $bazRes->getDescription());
    }
}