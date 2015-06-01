<?php

namespace IDcut\Jash\Object;

interface JSONObjectInterface
{
    static function build(Array $input);
    public function __toString();
}
