<?php

namespace App\Dice;

class Dice
{
    protected $value;

    public function __construct()
    {
        $this->value = random_int(1, 6);
    }

    public function roll(): int
    {
        $this->value = random_int(1, 6);
        return $this->value;
    }

    public function getAsString(): string
    {
        return "[{$this->value}]";
    }
    /**
     * Get the value of the last roll.
     *
     * @return int as value rolled.
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
