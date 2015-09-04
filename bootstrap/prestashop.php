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

$context = Context::getContext();
$lang = $context->language->iso_code=='en'?'en; q=1.0':$context->language->iso_code.'; q=1.0, en; q=0.5';

$apiClient = new \IDcut\Jash\APIClient\V1\Prestashop();
$apiClient->setAccessToken($config->getEncrypted("PS_IDCUT_API_TOKEN"));
$apiClient->setHttpClient(new \IDcut\Jash\Http\Client([
        'base_url' => $apiClient->getServiceUrl(),
        'defaults' => [
            'verify' => false
            ]
        ]),$lang);

$view = new \IDcut\Jash\Template\Basic();
$view->setTemplateDir(dirname(__FILE__) . '/../theme/prestashop/basic');
$core->setView($view);

$core->setApiClient($apiClient);

return $core;


