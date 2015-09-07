<?php
include(dirname(__FILE__).'/../../../config/config.inc.php');
$core = require_once dirname(__FILE__).'/../bootstrap/prestashop.php';

$code       = Tools::getValue("code");
$state      = Tools::getValue("state");
$savedState = $core->config()->getEncrypted("PS_IDCUT_OAUTH_STATE");

$result = "Try again";
$moduleName = "idcut";

/*
  eecho $moduleLink = Context::getContext()->link->getAdminLink('AdminModules', false) . '&configure=' . $moduleName . '&tab_module=' . $moduleName. '&module_name=' . $moduleName;
 *
 */

if (!$code) {
    exit('Invalid code');
} elseif (empty($state) || ($state !== $savedState)) {
    exit('Invalid state');
} else {

    try {

        $token = $core->getOAuthProvider()->getAccessToken('authorization_code',
            [
            'code' => $code
        ]);

        if ($token) {
            $core->config()->setEncrypted("PS_IDCUT_API_TOKEN", $token);
            $result = "Token saved";

            //updaing store information
            $store = new \IDcut\Jash\Object\Store\Store();

            $store->setPayment_return_url(Context::getContext()->link->getPageLink('index').'?fc=module&module='.$moduleName.'&controller=transaction');
            $store->setHook_url(Context::getContext()->link->getPageLink('index').'?fc=module&module='.$moduleName.'&controller=status_update');
            $store->setJoin_deal_url(Context::getContext()->link->getPageLink('index').'fc=module&module='.$moduleName.'&controller=deal_with_it&deal_hash=%s');

            $res = $core->getApiClient()->put('/store', $store);


            //$tokenInfo = var_export($core->getApiClient()->setAccessToken($token)->getTokenInfo()->json(), 1);
        }
    } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
        echo $e->getMessage();
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
        <a onclick="window.opener.location.href = window.opener.location.href; self.close();" href="">Close this window</a>
        <script>
            /*
            window.opener.location.href = window.opener.location.href;
            self.close();
            */
        </script>
    </body>
</html>