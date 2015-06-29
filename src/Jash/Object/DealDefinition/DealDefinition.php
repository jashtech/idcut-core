<?php

namespace IDcut\Jash\Object\DealDefinition;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;
use IDcut\Jash\Object\Range\Range as Range;

class DealDefinition implements JSONObjectInterface
{
    private $id;
    /* private $start_date; */
    /* private $end_date; */
    private $active;
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
    
    public function getActive()
    {
        return $this->active;
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

    public function setActive($active)
    {
        $this->active = $active;
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

    public function addRange(Range $range)
    {
        $this->ranges[$range->getMin_participants_number()] = $range;
    }

    public function __toString()
    {
        $dealDefinition                    = array();
        $dealDefinition['active']             = $this->getActive();
        $dealDefinition['ttl']             = $this->getTtl();
        $dealDefinition['locktime']        = $this->getLocktime();
        $dealDefinition['user_max']        = $this->getUser_max();
        $dealDefinition['min_order_value'] = $this->getMin_order_value();
        $dealDefinition['range_type']      = $this->getRange_type();
        $dealDefinition['ranges']          = $this->getRanges();

        $dealDefinition = array_filter($dealDefinition);
        return json_encode(array("deal_definition" => $dealDefinition),
            JSON_UNESCAPED_SLASHES);
    }

    public function __toStringForCreate()
    {
        $deal_definition = array();

        $deal_definition['user_max']    = $this->getUser_max();
        $deal_definition['ttl'] = $this->getTtl();
        $deal_definition['locktime']           = $this->getLocktime();
        $deal_definition['range_type']         = $this->getRange_type();
        $deal_definition['min_order_value']         = $this->getMin_order_value();
        $ranges = $this->getRanges();

        $deal_definition['deal_definition_ranges_attributes']         = array();
        
        foreach($ranges as $range){
            $deal_definition['deal_definition_ranges_attributes'][] = array('min_participants_number' => $range->getMin_participants_number(),
                'discount_size' =>  $range->getDiscount_size());
        }

        $deal_definition = array_filter($deal_definition);
        return json_encode(array("deal_definition" => $deal_definition),
            JSON_UNESCAPED_SLASHES);
    }

    public static function build(Array $input)
    {
        $dealDefinition = new DealDefinition();
        $dealDefinition->setId($input['id']);
        $dealDefinition->setActive($input['active']);
        $dealDefinition->setTtl($input['ttl']);
        $dealDefinition->setLocktime($input['locktime']);
        $dealDefinition->setUser_max($input['user_max']);
        $dealDefinition->setMin_order_value($input['min_order_value']);
        $dealDefinition->setRange_type($input['range_type']);

        foreach ((array) $input['ranges'] as $range) {
            $dealDefinition->addRange(new Range($range['min_participants_number'],
                $range['discount_size']));
        }

        return $dealDefinition;
    }
}