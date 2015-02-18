<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');
$core = require_once dirname(__FILE__) . '/../bootstrap/prestashop.php';

$code = Tools::getValue("code");
$state = Tools::getValue("state");
$savedState = $core->config()->getEncrypted("PS_KICKASS_OAUTH_STATE");

if (!$code) {
    exit('Invalid code');
} elseif (empty($state) || ($state !== $savedState)) {
    exit('Invalid state');
} else {
    $token = $core->getOAuthProvider()->getAccessToken('authorization_code', [
        'code' => $code
    ]);

    $core->config()->setEncrypted("PS_KICKASS_API_TOKEN", $token);
}

$core->config()->deleteByName("PS_KICKASS_OAUTH_STATE");
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>OAuth2 verification</title>

    </head>
    <body>

    </body>
</html>