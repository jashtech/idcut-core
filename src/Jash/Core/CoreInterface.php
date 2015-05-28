<?php

namespace IDcut\Jash\Core;

interface CoreInterface
{

    public function setOAuthProviderBuilder(\IDcut\Jash\OAuth2\Client\Provider\IDcutBuilder $oAuthProviderBuilde);

    public function getOAuthProviderBuilder();

    public function setConfig(\IDcut\Jash\Config\ConfigInterface $config);

    public function getConfig();

    public function config();

    public function setApiClient(\IDcut\Jash\APIClient\IDcutInterface $apiClient);

    public function getApiClient();

    public function setView(\IDcut\Jash\Template\TemplateInterface $view);

    public function getView();

    public function view();
}
