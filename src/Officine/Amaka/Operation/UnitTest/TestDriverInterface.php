<?php

namespace Officine\Amaka\Operation\UnitTest;

interface TestDriverInterface
{
    public function run();
    public function testDirectory($directory);
    public function useConfigFile($filePath);
    public function useBootstrapFile($filePath);
}
