<?php

class AdminKickassController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display   = 'view';

        $this->meta_title = $this->l('Your deal settings');
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
        $this->toolbar_title[] = $this->l('Deal settings');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->toolbar_btn['back']);
    }

    public function renderView()
    {
        $info                = Tools::getValue('updated_info', 0);
        $this->tpl_view_vars = array(
            'example' => 'example var',
            'info' => $info,
        );

        if (version_compare(_PS_VERSION_, '1.5.6.0', '>'))
                $this->base_tpl_view = 'view.tpl';
        return parent::renderView();
    }
}