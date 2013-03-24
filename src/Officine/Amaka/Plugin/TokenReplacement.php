<?php

namespace Officine\Amaka\Plugin;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;

use Officine\Amaka\PluginInterface;

/**
 * Token Replacement plugin
 */
class TokenReplacement implements PluginInterface
{
    private $map = array();

    /**
     * Interpret the value of $token
     *
     * Silently returns null when $token is undefined.
     *
     * @return mixed|null
     */
    public function interpret($token)
    {
        return $this->map[$token];
    }

    /**
     * Bind a $value to a particular $token
     *
     * @param string $token
     * @param mixed $value
     * @return $this
     */
    public function bind($token, $value)
    {
        $this->map[$token] = $value;

        return $this;
    }

    /**
     * Replace the tokens contained inside $file.
     *
     * The file is saved under the same name after processing, if you
     * need to replace the tokens and save under a different file name
     * please {@see replaceFromInto($source, $destination)}
     *
     * @param string $file      Replace tokens into the specified file
     * @param Finder $file      Replace tokens into all files from the Finder
     * @param SplFileInfo $file Replace tokens into the specified file
     *
     * @return $this
     */
    public function replaceInto($file)
    {
        $filename = $file;

        if ($file instanceof SymfonyFinder) {
            foreach ($file as $f) {
                $this->replaceInto($f);
            }
            return;
        }

        if ($file instanceof SplFileInfo) {
            $filename = $file->getRealPath();
        }

        $toks = array_keys($this->map);
        $vals = array_values($this->map);

        $content = file_get_contents($filename);
        $updated = str_replace($toks, $vals, $content);

        file_put_contents($filename, $updated);

        return $this;
    }

    /**
     * Replace the tokens contained in $source inside a the new $destination file.
     *
     * @param string $source
     * @param string $destination
     */
    public function replaceFromInto($source, $destination)
    {
        if (copy($source, $destination)) {
            $this->replaceInto($destination);
        }
        return $this;
    }
}