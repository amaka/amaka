<?php

namespace Officine\Amaka\ErrorReporting;

class Reporter
{
    private $print;
    private $printLine;

    public function setPrintFunction($print)
    {
        $this->print = $print;
    }

    public function setPrintLineFunction(callable $printLine)
    {
        $this->printLine = $printLine;
    }

    public function handleException($e)
    {
        if ($e instanceof ErrorInterface) {
            return call_user_func($this->printLine, Formatter\ErrorFormatter::format($e));
        }
        throw $e;
    }
}
