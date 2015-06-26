<?php

class IDcutDeal_With_ItModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        $this->action = 'view';
        parent::__construct();
    }

    // this calls proper action
    public function postProcess()
    {
        $deal_hash = Tools::getValue('deal_hash');
        if (Validate::isReference($deal_hash) && !empty($deal_hash)) {
            $this->context->cookie->__set('deal_hash', $deal_hash);
        }
    }
}