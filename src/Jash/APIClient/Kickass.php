<?php

namespace Kickass\Jash\APIClient;

abstract class Kickass implements KickassInterface {
    protected $version;
    
    public function setVersion($version){
        $this->version = $version;
    }
    
    public function getVErsion(){
        return $this->version;
    }
    
}
