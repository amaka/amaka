<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginBroker;
use Officine\Amaka\PluginInterface;

class Directories implements PluginInterface
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

    public function move($source, $destination)
    {
        rename($this->abs($source), $this->abs($destination));
    }

    public function create($directory)
    {
        $path = $this->abs($directory);

        if (! file_exists($path)) {
            mkdir($path);
        }
    }

    public function remove($directory)
    {
        $this->workingDirectoryCheck();

        $path = $this->abs($directory);

        if (! file_exists($path)) {
            return;
        }

        $r = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $i = new \RecursiveIteratorIterator($r, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($i as $child) {
            if ($child->isFile()) {
                unlink($child->getPathName());
            } else if ($child->isDir()) {
                rmdir($child->getPathName());
            }
        }
        rmdir($path);
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
