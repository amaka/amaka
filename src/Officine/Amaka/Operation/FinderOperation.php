<?php

namespace Officine\Amaka\Operation;

use Symfony\Component\Finder\Finder;

class FinderOperation implements OperationInterface
{
    public function invoke()
    {
        return new Finder();
    }
}
