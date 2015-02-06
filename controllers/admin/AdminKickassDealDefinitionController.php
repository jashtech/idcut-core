<?php

class AdminKickassDealDefinitionController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display   = 'view';

        $this->meta_title = $this->l('Your Kickass Deal Definition');
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
        $this->toolbar_title[] = $this->l('Kickass Deal Definition');
    }


    public function renderView()
    {
        $view = $this->module->core->getView();
        $view->setTemplateFile("adminDealDefinition.php");
        $view->zmienna = "Deal Definition";
        return $view->render();
        
    }
}