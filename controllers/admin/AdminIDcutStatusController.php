<?php

class AdminIDcutStatusController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display   = 'view';

        $this->meta_title = $this->l('Your IdealCutter Status');
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
        $this->toolbar_title[] = $this->l('IdealCutter Status');
    }

    public function renderView()
    {
        $view = $this->module->core->getView();
        $view->setTemplateFile("adminStatus.php");
        $view->heading = $this->l('IdealCutter client status');

        try {
            $storeResponse = $this->module->core->getApiClient()->get('/store');
            $storeJson = $storeResponse->json();
            $store     = IDcut\Jash\Object\Store\Store::build($storeJson);
            Configuration::updateValue('PS_IDCUT_SA', $store->getActive() === true);
            $view->store_active = $store->getActive();
            if((bool)$view->store_active){
                $view->message = $this->l('Store is active and ready to work');
            }else{
                $view->message = $this->l('Store is not active You need to go the whole configuration process and notify admin of IdealCutter');
            }
        } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
            $view->error = $this->l('Can\'t connect with api');

            // 1.5 handles apostrophes incorrectly when calculating MD5
            if (!$this->module->ps_above_16) {
                global $_MODULES;
                $key = Tools::strtolower(
                    '<{' . $this->module->name . '}prestashop>' . get_class() . '_' . md5($view->error)
                );
                $view->error = isset($_MODULES[$key]) ? $_MODULES[$key] : $view->error;
            }
        }

        return $view->render();
    }
}