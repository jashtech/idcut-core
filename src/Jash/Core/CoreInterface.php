<?php

namespace Kickass\Jash\Core;

interface CoreInterface
{

    public function setOAuthProviderBuilder(\Kickass\Jash\OAuth2\Client\Provider\KickassBuilder $oAuthProviderBuilde);

    public function getOAuthProviderBuilder();

    public function setConfig(\Kickass\Jash\Config\ConfigInterface $config);

    public function getConfig();

    public function config();

    public function setApiClient(\Kickass\Jash\APIClient\KickassInterface $apiClient);

    public function getApiClient();

    public function setView(\Kickass\Jash\Template\TemplateInterface $view);

    public function getView();

    public function view();
}
