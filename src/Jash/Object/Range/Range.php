<?php

namespace IDcut\Jash\Object\Range;

class Range
{
    private $min_participants_number;
    private $discount_size;

    public function __construct($min_participants_number, $discount_size)
    {
        $this->discount_size = $min_participants_number;
        $this->discount_size = $discount_size;
    }

    public function getMin_participants_number()
    {
        return $this->min_participants_number;
    }

    public function getDiscount_size()
    {
        return $this->discount_size;
    }

    public function setMin_participants_number($min_participants_number)
    {
        $this->min_participants_number = $min_participants_number;
    }

    public function setDiscount_size($discount_size)
    {
        $this->discount_size = $discount_size;
    }
}