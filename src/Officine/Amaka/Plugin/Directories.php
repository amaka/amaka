<?php

namespace Officine\Amaka\Plugin;

class Directories
{
    private $workingDirectory;

    public function workingDirectoryCheck()
    {
        // testing when $this->workingDirectory is a directory that
        // doesn't actually exist.  this should only be done when
        // setting the working directory. I'll have to move to a different
        // method and consequently write the context
        if (! $this->workingDirectory || ! file_exists($this->workingDirectory)) {
            throw new \BadMethodCallException("Please set the working directory with 'setWorkingDirectory()' before using the plugin.");
        }
    }

    public function create()
    {
        $this->workingDirectoryCheck();
    }

    public function remove()
    {
        $this->workingDirectoryCheck();
    }

    public function setWorkingDirectory($directory)
    {
        $this->workingDirectory = $directory;
        return $this;
    }

    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }
}
