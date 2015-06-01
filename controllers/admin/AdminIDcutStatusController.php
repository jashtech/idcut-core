<?php

class AdminIDcutStatusController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display   = 'view';

        $this->meta_title = $this->l('Your IDcut Status');
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
        $this->toolbar_title[] = $this->l('IDcut Status');
    }

    public function renderView()
    {
        $view = $this->module->core->getView();
        $view->setTemplateFile("adminStatus.php");

        $dump = new stdClass();

        $view->coreClass      = get_class($this->module->core);
        $view->configClass    = get_class($this->module->core->getConfig());
        $view->apiClientClass = get_class($this->module->core->getApiClient());
        $view->cipherClass    = get_class($this->module->core->getCipher());
        $view->authenticated  = "maybe";
        $view->accessToken    = $this->module->core->config()->getEncrypted("PS_IDCUT_API_TOKEN");

        $tokenInfo = $this->module->core->getApiClient()->getTokenInfo();
        if ($tokenInfo instanceof GuzzleHttp\Message\Response) {
            $view->accessTokenInfo = var_export($tokenInfo->json(), true);
        }


        $view->apiClientVersion = $this->module->core->getApiClient()->getVersion();
        $view->serviceUrl       = $this->module->core->getApiClient()->getServiceUrl();
        $view->cipherTest       = $this->module->core->getCipher()->test(md5(rand()))
                ? "OK" : "FAIL";

        $testResponse = $this->module->core->getApiClient()->test();
        if ($testResponse instanceof GuzzleHttp\Message\Response) {
            $view->testResponse = var_export($testResponse, true);
        }


        try {
            $storeResponse = $this->module->core->getApiClient()->get('/store');
        } catch (\Exception $e) {
            echo "Problem";
        }

        if ($storeResponse) {
            $storeJson = $storeResponse->getBody();
            $store = IDcut\Jash\Object\Store\Store::build($storeJson);

            $dump->storeBuild = $store;
            $dump->storeJson = $store->__toString();
        }




        $view->dump = $dump;

        return $view->render();
    }
}