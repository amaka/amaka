<?php

namespace Officine\Amaka\ErrorReporting;

interface ErrorInterface
{
    public function getLongMessage();
    public function getResolutions();
}