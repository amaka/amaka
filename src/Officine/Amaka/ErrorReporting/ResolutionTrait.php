<?php

namespace Officine\Amaka\ErrorReporting;

trait ResolutionTrait
{
    private $resolutions = [];

    public function getResolutions()
    {
        return $this->resolutions;
    }

    public function addResolution($resolution)
    {
        $name = $resolution;
        $description = "";
        $unpack = function($items) {
            if (count($items) == 1) {
                return each($items);
            }
            return $items;
        };

        if (is_array($resolution)) {
            list($name, $description) = $unpack($resolution);
        }
        $this->resolutions[] = new Resolution($name, $description);
        return $this;
    }

    public function addResolutions(array $resolutions)
    {
        array_walk($resolutions, [$this, 'addResolution']);
        return $this;
    }
}
