<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginBroker;
use Officine\Amaka\PluginInterface;

class Directories implements PluginInterface
{
    private $workingDirectory;

    public function __construct($wd = null)
    {
        $this->setWorkingDirectory($wd);
    }

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

    private function abs($directory = null)
    {
        if (null === $directory) {
            return $this->getWorkingDirectory();
        }

        return $this->getWorkingDirectory()
             . DIRECTORY_SEPARATOR
             . $directory;
    }

    public function copy($source, $dest)
    {
        return copy($source, $dest);
    }

    public function move($source, $dest)
    {
        $pDest = $this->abs($dest);
        $pSource = $this->abs($source);

        if (! is_dir($pSource)) {
            throw new \InvalidArgumentException("Cannot move directory '{$pSource}': not a valid source directory.");
        }

        if (file_exists($pDest) && ! is_dir($pDest)) {
            throw new \InvalidArgumentException("Cannot move directory '{$pDest}': not a valid destination directory.");
        }

        //if (is_dir($pDest)) {
        //    throw new \InvalidArgumentException("Cannot move directory '{$pSource}' to '{$pDest}': directory exists.");
        //}

        if (is_dir($pDest)) {
            //echo "Copying '$pSource' into '$pDest'\n";
            $r = new \RecursiveDirectoryIterator($pSource, \FilesystemIterator::SKIP_DOTS);
            $i = new \RecursiveIteratorIterator($r, \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($i as $nSource) {
                $nDest = str_replace($this->abs($source), $this->abs($dest), $nSource);
                //echo "Moving $nSource to $nDest\n";
                //rename($nSource, $nDest);
                if (! file_exists($nDest) && is_dir($nSource)) {
                    mkdir($nDest);
                } else if (is_file($nSource)) {
                    copy($nSource, $nDest);
                }
            }
        } else {
            rename($pSource, $pDest);
        }

        return $this;
    }

    public function create($directory)
    {
        $path = $this->abs($directory);

        if (file_exists($path)) {
            throw new \InvalidArgumentException("Cannot create directory '{$path}': file already exists.");
        }

        mkdir($path);

        //if (! is_dir($path)) {
        //    throw new \RuntimeException("Could not create directory '{$path}'.");
        //}

        return $this;
    }

    public function remove($directory)
    {
        $this->workingDirectoryCheck();

        $path = $this->abs($directory);

        if (! file_exists($path)) {
            throw new \InvalidArgumentException("Cannot remove directory '{$path}': directory does not exist.");
        }

        if (! is_dir($path)) {
            throw new \InvalidArgumentException("Cannot remove directory '{$path}': not a valid directory.");
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

        return $this;
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
