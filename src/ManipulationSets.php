<?php

namespace Spatie\Image;

use IteratorAggregate;

class ManipulationSets implements IteratorAggregate
{
    protected $manipulationSets = [];

    protected $openNewSet = true;

    public function addManipulation(string $operation, $argument)
    {
        if ($this->openNewSet) {
            $this->manipulationSets[] = [];
        }

        $lastIndex = count($this->manipulationSets) - 1;

        $this->manipulationSets[$lastIndex][$operation] = $argument;

        $this->openNewSet = false;
    }

    public function startNewSet()
    {
        $this->openNewSet = true;
    }

    public function getSets(): array
    {
        return $this->manipulationSets;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->manipulationSets);
    }
}