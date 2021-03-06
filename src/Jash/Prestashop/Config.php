<?php

namespace IDcut\Jash\Prestashop;

class Config extends \IDcut\Jash\Config\ConfigAbstract{
    
    public function get($key){
        return \Configuration::get($key);
    }
    
     public function set($key , $value){
        return \Configuration::updateValue($key, $value);
    }
    
    public function update($key , $value){
        return \Configuration::updateValue($key, $value);
    }
    
    public function deleteByName($key){
        return \Configuration::deleteByName($key);
    }
    
}