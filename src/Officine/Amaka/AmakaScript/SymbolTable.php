<?php

namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\Invocable;

class SymbolTable
{
    private $symbols = [];

    public function addSymbol($symbolName, $symbolsRequired = [])
    {
        if (is_string($symbolsRequired)) {
            $symbolsRequired = [$symbolsRequired];
        }

        if (is_array($symbolsRequired)) {
            foreach ($symbolsRequired as $rSymbolName) {
                $this->addSymbol($rSymbolName);
            }
        }

        if ($this->hasSymbol($symbolName)) {
            $symbolsRequired = array_unique(array_merge($this->getSymbolsRequiredBy($symbolName), $symbolsRequired));
        }

        $this->symbols[$this->symbolName($symbolName)] = $symbolsRequired;
        return $this;
    }

    public function getSymbolsRequiredBy($symbolName)
    {
        if ($this->hasSymbol($symbolName)) {
            return $this->symbols[$this->symbolName($symbolName)];
        }
        return [];
    }

    public function addRequisiteToSymbol($symbolName, $symbolRequired)
    {
        if ($this->hasSymbol($symbolName)) {
            if (! $this->hasSymbol($symbolRequired)) {
                $this->addSymbol($symbolRequired);
            }
            array_push($this->symbols[$this->symbolName($symbolName)], $symbolRequired);
        }
        return $this;
    }

    public function mergeWith(SymbolTable $table)
    {
        foreach ($table->getAllSymbols() as $symbolName => $symbolsRequired) {
            $this->addSymbol($symbolName, $symbolsRequired);
        }
        return $this;
    }

    public function getAllSymbols()
    {
        return $this->symbols;
    }

    public function hasSymbol($symbolName)
    {
        return isset($this->symbols[$this->symbolName($symbolName)]);
    }

    private function symbolName($symbol)
    {
        if ($symbol instanceof Invocable) {
            return $symbol->getName();
        }
        return $symbol;
    }
}
