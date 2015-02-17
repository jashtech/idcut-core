<?php

namespace Kickass\Jash\Cipher;

class Rijndael extends \Crypt_Rijndael implements CipherInterface
{

    public function test($value)
    {
        $encrypted = $this->encrypt($value);
        $armored = base64_encode($encrypted);
        $encrypted = base64_decode($armored);
        $decrypted = $this->decrypt($encrypted);

        return $decrypted === $value;
    }

}
