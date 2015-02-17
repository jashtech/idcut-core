<?php

class AdminKickassStatusController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display   = 'view';

        $this->meta_title = $this->l('Your Kickass Status');
        parent::__construct();
        if (!$this->module->active)
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));

        $this->core = require dirname(__FILE__) . '/../../bootstrap/prestashop.php';

    }

    public function setMedia()
    {
        return parent::setMedia();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Kickass Status');
    }

    public function renderView()
    {
        $view          = $this->module->core->getView();
        $view->setTemplateFile("adminStatus.php");

        $view->coreClass = get_class ($this->core);
        $view->configClass = get_class ($this->core->getConfig());
        $view->apiClientClass = get_class($this->core->getApiClient());
        $view->cipherClass = get_class($this->core->getCipher());
        $view->authenticated = "maybe";
        $view->accessToken = $this->core->config()->getEncrypted("PS_KICKASS_API_TOKEN");
        $view->accessTokenInfo = var_export($this->core->getApiClient()->getTokenInfo()->json() , 1);
        $view->apiClientVersion = $this->core->getApiClient()->getVersion();
        $view->serviceUrl = $this->core->getApiClient()->getServiceUrl();
        $view->cipherTest = $this->core->getCipher()->test(md5(rand())) ? "OK" : "FAIL";
        $view->testResponse = var_export($this->core->getApiClient()->test(),1);
        return $view->render();
    }
}