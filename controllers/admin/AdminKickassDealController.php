<?php

class AdminKickassDealController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap  = true;
        $this->table      = 'kickass_deal';
        $this->className  = 'KickassDeal';
        $this->lang       = false;
//        $this->addRowAction('view');
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your Kickass Deals');

        $this->_orderBy = 'created_at';
        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_kickass_deal' => array('title' => $this->l('ID')),
            'deal_id' => array('title' => $this->l('Deal ID')),
            'id_kickass_deal_definition' => array('title' => $this->l('Definition ID')),
            'created_at' => array('title' => $this->l('Created at')),
            'hash_id' => array('title' => $this->l('Hash ID')),
        );
        
        parent::__construct();
        if (!$this->module->active)
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
    }

    public function setMedia()
    {
        return parent::setMedia();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Kickass Deals');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset ($this->toolbar_btn['new']);
    }

}