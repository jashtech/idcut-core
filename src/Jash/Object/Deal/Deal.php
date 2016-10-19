<?php

namespace IDcut\Jash\Object\Deal;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;

class Deal implements JSONObjectInterface
{
    private $id;
    private $created_at;
    private $updated_at;
    private $state;
    private $ended;
    private $end_date;
    private $hash_id;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getEnded()
    {
        return $this->ended;
    }

    public function getEnd_date()
    {
        return $this->end_date;
    }

    public function getHash_id()
    {
        return $this->hash_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function setEnded($ended)
    {
        $this->ended = $ended;
    }

    public function setEnd_date($end_date)
    {
        $this->end_date = $end_date;
    }

    public function setHash_id($hash_id)
    {
        $this->hash_id = $hash_id;
    }

    public function __toString()
    {
        $deal               = array();
        $deal['id']         = $this->getId();
        $deal['created_at'] = $this->getCreated_at();
        $deal['updated_at'] = $this->getUpdated_at();
        $deal['state']      = $this->getState();
        $deal['ended']      = $this->getEnded();
        $deal['end_date']   = $this->getEnd_date();
        $deal['hash_id']    = $this->getHash_id();

        $deal = array_filter($deal);
        return json_encode(array("deal" => $deal));
    }

    public function __toStringForCreate()
    {
        $deal = array();

        $deal['user_max']        = $this->getUser_max();
        $deal['ttl']             = $this->getTtl();
        $deal['locktime']        = $this->getLocktime();
        $deal['range_type']      = $this->getRange_type();
        $deal['min_order_value'] = $this->getMin_order_value();

        $deal = array_filter($deal);
        return json_encode(array("deal" => $deal));
    }

    public static function build(Array $input)
    {
        $deal = new Deal();
        $deal->setId($input['id']);
        $deal->setCreated_at($input['created_at']);
        $deal->setUpdated_at($input['updated_at']);
        $deal->setState($input['state']);
        $deal->setEnded($input['ended']);
        $deal->setEnd_date($input['end_date']);
        $deal->setHash_id($input['hash_id']);

        return $deal;
    }
}