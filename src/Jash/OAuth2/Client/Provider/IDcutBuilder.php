<?php

namespace IDcut\Jash\OAuth2\Client\Provider;

class IDcutBuilder {

    private $provider;
    private $config;

    public function __construct(\IDcut\Jash\Config\ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function buildProvider()
    {
        $this->provider = new IDcut(array(
            'clientId' => $this->config->get("PS_IDCUT_CLIENT_ID"),
            'clientSecret' => $this->config->getEncrypted("PS_IDCUT_CLIENT_SECRET"),
            'redirectUri' => $this->config->get("PS_IDCUT_REDIRECT_URL"),
            'scopes' => array()
        ));

        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

}
