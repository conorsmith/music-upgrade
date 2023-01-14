<?php

namespace App\Models;

class Rating
{
    public static function fromString($valueAsString)
    {
        return new self($valueAsString);
    }

    private $value;

    public function __construct($value)
    {
        $this->value = intval($value);
    }

    public function getValue()
    {
        return $this->value;
    }
}
