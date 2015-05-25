<?php

class AdminKickassTransactionController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap  = true;
        $this->table      = 'kickass_transaction';
        $this->className  = 'KickassTransaction';
        $this->lang       = false;
        $this->addRowAction('view');
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your Kickass Transaction');

        $this->_orderBy = 'date_edit';
        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_kickass_transaction' => array('title' => $this->l('ID')),
            'id_order' => array('title' => $this->l('Order ID')),
            'transaction_id' => array('title' => $this->l('Transaction ID')),
            'status' => array('title' => $this->l('Status')),
            'error_code' => array('title' => $this->l('Error code')),
            'date_edit' => array('title' => $this->l('Last update')),
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
        $this->toolbar_title[] = $this->l('Kickass Transactions');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset ($this->toolbar_btn['new']);
    }

    public function renderView()
    {
        $this->loadObject(true);
        $this->tpl_view_vars = array(
            'transaction' => $this->object
        );
        
        return parent::renderView();
    }

}