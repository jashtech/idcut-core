<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');
$core = require_once dirname(__FILE__) . '/../bootstrap/prestashop.php';

$code = Tools::getValue("code");
$state = Tools::getValue("state");
$savedState = $core->config()->getEncrypted("PS_IDCUT_OAUTH_STATE");

if (!$code) {
    exit('Invalid code');
} elseif (empty($state) || ($state !== $savedState)) {
    exit('Invalid state');
} else {
    $token = $core->getOAuthProvider()->getAccessToken('authorization_code', [
        'code' => $code
    ]);

    if ($token) {
        $core->config()->setEncrypted("PS_IDCUT_API_TOKEN", $token);
        $result = "Token saved";
        //$tokenInfo = var_export($core->getApiClient()->setAccessToken($token)->getTokenInfo()->json(), 1);
    }
}

//$core->config()->deleteByName("PS_IDCUT_OAUTH_STATE");
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>OAuth2 verification</title>

    </head>
    <body>
        <h2><?php echo $result; ?></h2>
    </body>
</html>