<?php

namespace Kickass\Jash\APIClient;

abstract class KickassAbstract implements KickassInterface
{

    protected $version;
    protected $serviceUrl = "http://api.kickass.jash.fr";
    protected $accessToken;

    public function getVersion()
    {
        return $this->version;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

}
