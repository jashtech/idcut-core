<?php

namespace Kickass\Jash\APIClient;

abstract class KickassAbstract implements KickassInterface {

    protected $version;
    protected $serviceUrl = "http://api.kickass.jash.fr";

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVErsion()
    {
        return $this->version;
    }

}
