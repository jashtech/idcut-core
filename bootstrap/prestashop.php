<?php

$loader = require(dirname(__FILE__) . '/../vendor/autoload.php');

$config = new \IDcut\Jash\Prestashop\Config();

$cipher = new IDcut\Jash\Cipher\Rijndael(CRYPT_RIJNDAEL_MODE_ECB);

$cipher->setKey(_RIJNDAEL_KEY_);
$config->setCipher($cipher);

$core = new IDcut\Jash\Prestashop\Core();

$core->setConfig($config);
$core->setCipher($cipher);
$core->setOAuthProviderBuilder(new \IDcut\Jash\OAuth2\Client\Provider\IDcutBuilder($config));

$apiClient = new \IDcut\Jash\APIClient\V1\IDcut();
$apiClient->setAccessToken($config->getEncrypted("PS_IDCUT_API_TOKEN"));
$apiClient->setHttpClient(new \IDcut\Jash\Http\Client([
        'base_url' => $apiClient->getServiceUrl(),
        'defaults' => [
            'verify' => false
            ]
        ]));

$view = new \IDcut\Jash\Template\Basic();
$view->setTemplateDir(dirname(__FILE__) . '/../theme/prestashop/basic');
$core->setView($view);

$core->setApiClient($apiClient);

return $core;


