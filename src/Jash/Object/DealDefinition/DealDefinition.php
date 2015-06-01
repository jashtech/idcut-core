<?php

namespace IDcut\Jash\DealDefinition;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;

class DealDefinition implements JSONObjectInterface
{
    private $id;
    /* private $start_date; */
    /* private $end_date; */
    private $ttl;
    private $locktime;
    private $user_max;
    private $min_order_value;
    private $range_type;
    private $ranges = [];

    public function getId()
    {
        return $this->id;
    }

    public function getTtl()
    {
        return $this->ttl;
    }

    public function getLocktime()
    {
        return $this->locktime;
    }

    public function getUser_max()
    {
        return $this->user_max;
    }

    public function getMin_order_value()
    {
        return $this->min_order_value;
    }

    public function getRange_type()
    {
        return $this->range_type;
    }

    public function getRanges()
    {
        return $this->ranges;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    public function setLocktime($locktime)
    {
        $this->locktime = $locktime;
    }

    public function setUser_max($user_max)
    {
        $this->user_max = $user_max;
    }

    public function setMin_order_value($min_order_value)
    {
        $this->min_order_value = $min_order_value;
    }

    public function setRange_type($range_type)
    {
        $this->range_type = $range_type;
    }

    public function setRanges($ranges)
    {
        $this->ranges = $ranges;
    }

    public function __toString()
    {
        
    }

    public static function build($json)
    {

    }
}