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

    public function create($directory)
    {
        $path = $this->abs($directory);

        if (! file_exists($path)) {
            mkdir($path);
        }
    }

    public function exists($directory)
    {
        return file_exists($this->abs($directory));
    }

    private function abs($directory)
    {
        return $this->getWorkingDirectory()
             . DIRECTORY_SEPARATOR
             . $directory;
    }

    public function remove($directory)
    {
        $this->workingDirectoryCheck();

        $r = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
        $i = new RecursiveIteratorIterator($r);
        foreach ($i as $e) {
            if ($e->isFile()) {
                unlink($e);
            } else if ($e->isDir()) {
                rmdir($this->abs($e));
            }

        }
        rmdir($this->abs($directory));
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
