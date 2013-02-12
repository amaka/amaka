<?php

namespace Officine\Amaka\Plugin;

use Officine\Amaka\PluginInterface;

class TokenReplacement implements PluginInterface
{
    private $map = array();

    public function interpret($token)
    {
        return $this->map[$token];
    }

    public function bind($token, $value)
    {
        $this->map[$token] = $value;

        return $this;
    }

    public function replaceInto($file)
    {
        $toks = array_keys($this->map);
        $vals = array_values($this->map);
        $content = file_get_contents($file);

        $updated = str_replace($toks, $vals, $content);

        file_put_contents($file, $updated);
    }

    public function replaceFromInto($source, $destination)
    {
        if (copy($source, $destination)) {
            $this->replaceInto($destination);
        }
    }
}