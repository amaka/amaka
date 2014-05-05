<?php

namespace Officine\StdLib;

class CommandBuilder
{
    private $prefix;
    private $arguments = [];

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function addArgument($argName, $argValue = null)
    {
        $escapedName = escapeshellarg($argName);
        if ($argValue) {
            $escapedValue = escapeshellarg($argValue);
            $this->arguments[] = [$escapedName, $escapedValue];
            return $this;
        }
        $this->arguments[] = $escapedName;
        return $this;
    }

    public function getCommandString()
    {
        return array_reduce(
            $this->arguments,
            function($command, $argument) {
                if (is_array($argument)) {
                    return $command . ' ' . implode(' ', $argument);
                }
                return $command . ' ' . $argument;
            },
            escapeshellcmd($this->prefix)
        );
    }
}
