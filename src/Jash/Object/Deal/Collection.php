<?php

namespace IDcut\Jash\Object\Deal;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;
use Deal;

class Collection implements JSONObjectInterface
{
    private $deals = array();

    public function addDeal(Deal $deal)
    {
        $this->deals[$deal->getId()] = $deal;
    }

    public function getDeal($id)
    {
        if (isset($this->deals[$id])) {
            return $this->deals[$id];
        }
    }

    public function getAll()
    {
        return $this->deals;
    }

    public function __toString()
    {
        
    }

    public static function build(array $input)
    {

        $collection = new Collection();

        foreach ($input as $index => $dd) {
            $collection->addDeal(Deal::build($dd));
        }

        return $collection;
    }
}