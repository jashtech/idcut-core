<?php

class IDcutDeal_With_ItModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    
    public function __construct()
    {
        $this->action = 'view';
        parent::__construct();
    }
    
    public function setMedia()
    {
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/css/front/deal_with_it.css');
        return parent::setMedia();
    }
    
    // this calls proper action
    public function postProcess()
    {
        $deal_hash = Tools::getValue('deal_hash');
        if (Validate::isReference($deal_hash) && !empty($deal_hash)) {
            $this->context->cookie->__set('deal_hash', $deal_hash);
        }
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $this->context->smarty->assign(array(
            'deal_hash' => Tools::getValue('deal_hash',
                $this->context->cookie->deal_hash),
        ));

        $this->setTemplate('deal_with_it.tpl');
    }
}