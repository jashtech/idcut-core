<?php

namespace Kickass\Jash\Config;

abstract class ConfigAbstract implements ConfigInterface 
{

    private $cipher;
    
    abstract public function set($key , $value);
    abstract public function get($key);

    public function update($key, $value)
    {
        
    }

    public function delete($key)
    {
        
    }

    public function setCipher(\Kickass\Jash\Cipher\CipherInterface $cipher)
    {
        $this->cipher = $cipher;
        return $this;
    }
    
    public function setEncrypted($key, $value){
        if($this->cipher){
            $encrypted = $this->cipher->encrypt($value);
            $armored = base64_encode($encrypted);
            $this->update($key, $armored);
        }
        
        return $this;
        
    }
    
    public function getEncrypted($key){
        if($this->cipher){
            $armored = $this->get($key);
            $encrypted = base64_decode($armored);
            $decrypted = '';
            if($encrypted){
                $decrypted = $this->cipher->decrypt($encrypted);
            }
            return $decrypted;
        }
    }
    

}
