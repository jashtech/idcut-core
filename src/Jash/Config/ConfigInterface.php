<?php

namespace IDcut\Jash\Config;

interface ConfigInterface{
    public function set($key, $value);
    public function get($key);
}