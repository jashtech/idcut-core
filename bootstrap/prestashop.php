<?php

$loader = require(dirname(__FILE__).'/../vendor/autoload.php');

$config = new \Kickass\Jash\Prestashop\Config();

$cipher = new Kickass\Jash\Cipher\Rijndael(CRYPT_RIJNDAEL_MODE_ECB);
$cipher->setKey(_RIJNDAEL_KEY_);
$config->setCipher($cipher);

$core = new Kickass\Jash\Prestashop\Core();
$core->setConfig( $config );
$core->setCipher( $cipher );
$core->setOAuthProviderBuilder( new \Kickass\Jash\OAuth2\Client\Provider\KickassBuilder( $config ));

return $core;


