<?php

namespace IDcut\Jash\Cipher;

interface CipherInterface {
    public function setKey($key);
    public function decrypt($input);
    public function encrypt($input);
}
