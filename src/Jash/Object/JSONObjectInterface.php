<?php

namespace IDcut\Jash\Object;

interface JSONObjectInterface
{
    static function build($json);
    public function __toString();
}
