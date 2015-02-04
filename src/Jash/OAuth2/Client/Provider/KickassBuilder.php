<?php

namespace Kickass\Jash\OAuth2\Client\Provider;

class KickassBuilder {

    private $provider;
    private $config;

    public function __construct(\Kickass\Jash\Config\ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function buildProvider()
    {
        $this->provider = new Kickass(array(
            'clientId' => $this->config->get("PS_KICKASS_CLIENT_ID"),
            'clientSecret' => $this->config->getEncrypted("PS_KICKASS_CLIENT_SECRET"),
            'redirectUri' => $this->config->get("PS_KICKASS_REDIRECT_URL"),
            'scopes' => array()
        ));

        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

}
