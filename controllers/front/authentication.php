<?php

class IDcutAuthenticationModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        $this->action = 'view';
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $this->display_header = false;
        $this->display_footer = false;
        $this->display_column_left = false;
        $this->display_column_right = false;
        
        $code       = Tools::getValue("code");
        $state      = Tools::getValue("state");
        $savedState = $this->module->core->config()->getEncrypted("PS_IDCUT_OAUTH_STATE");

        $this->result = 0;

        if (!$code) {
            $this->errors[] = $this->module->l('Invalid code');
        } elseif (empty($state) || ($state !== $savedState)) {
            $this->errors[] = $this->module->l('Invalid state');
        } else {
            try {

                $token = $this->module->core->getOAuthProvider()->getAccessToken('authorization_code',
                    array(
                    'code' => $code
                ));

                if ($token) {
                    $this->module->core->config()->setEncrypted("PS_IDCUT_API_TOKEN", $token);
                    $this->result = 1;

                    //updaing store information
                    $store = new \IDcut\Jash\Object\Store\Store();

                    $store->setPayment_return_url(Context::getContext()->link->getPageLink('index').'?fc=module&module='.$this->module->name.'&controller=transaction');
                    $store->setHook_url(Context::getContext()->link->getPageLink('index').'?fc=module&module='.$this->module->name.'&controller=status_update');
                    $store->setJoin_deal_url(Context::getContext()->link->getPageLink('index').'?fc=module&module='.$this->module->name.'&controller=deal_with_it&deal_hash=%s');

                    $res = $this->module->core->getApiClient()->put('/store', $store->__toStringUpdateUrls());


                    //$tokenInfo = var_export($this->module->core->getApiClient()->setAccessToken($token)->getTokenInfo()->json(), 1);
                }
            } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
        $this->context->smarty->assign(array(
            'resultInfo' => $this->result,
        ));

        $this->setTemplate('authentication.tpl');
    }
}