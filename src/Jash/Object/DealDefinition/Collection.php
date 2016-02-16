<?php

namespace IDcut\Jash\Object\DealDefinition;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;
use DealDefinition;

class Collection implements JSONObjectInterface
{
    private $dealDefinitions = array();

    public function addDealDefinition(DealDefinition $dealDefinition)
    {
        $this->dealDefinitions[$dealDefinition->getId()] = $dealDefinition;
    }

    public function getDealDefinition($id)
    {
        if (isset($this->dealDefinitions[$id])) {
            return $this->dealDefinitions[$id];
        }
    }

    public function getAll()
    {
        return $this->dealDefinitions;
    }

    public function __toString()
    {
        
    }

    public static function build(array $input)
    {

        $collection = new Collection();

        foreach ($input as $index => $dd) {
            $collection->addDealDefinition(DealDefinition::build($dd));
        }

        return $collection;
    }
}