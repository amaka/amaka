<?php

namespace Officine\StdLib;

/**
 * The FileResolver is a reusable component that makes easy to search
 * for a particular file inside different possible folders.
 *
 * @author Andrea Turso <andrea.turso@gmail.com>
 */
class FileResolver
{
    private $searchPaths = array();

    public function resolve($filename, $callback = null, $errback = null)
    {
        if (empty($this->searchPaths)) {
            throw new \RuntimeException("Make sure you've declared at least search path before calling resolve(\$filename).");
        }

        foreach ($this->searchPaths as $path) {
            $fullpath = $this->filter($path) . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($fullpath)) {
                $this->callOrIgnore($callback, $fullpath);
                return true;
            }
        }

        $this->callOrIgnore($errback, $filename);
        return false;
    }

    public function callOrIgnore($callback, $filename)
    {
        if (is_callable($callback)) {
            call_user_func($callback, $filename);
        }
    }

    public function filter($path)
    {
        return '/' === $path ? $path : rtrim($path, DIRECTORY_SEPARATOR);
    }

    public function addPath($path)
    {
        if ('' === $path) {
            throw new InvalidArgumentException("Cannot declare an empty search path.");
        }
        array_push($this->searchPaths, $path);

        return $this;
    }
}
