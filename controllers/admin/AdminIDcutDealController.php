<?php

class AdminIDcutDealController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap  = true;
        $this->table      = 'idcut_deal';
        $this->className  = 'IDcutDeal';
        $this->lang       = false;
        $this->addRowAction('view');
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your IdealCutter Deals');

        $this->_orderBy  = 'created_at';
        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_idcut_deal' => array('title' => $this->l('ID')),
            'deal_id' => array('title' => $this->l('Deal ID')),
            'deal_definition_id' => array('title' => $this->l('Definition ID')),
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
        $this->toolbar_title[] = $this->l('IdealCutter Deals');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->toolbar_btn['new']);
        $this->toolbar_btn['import'] = array(
            'href' => self::$currentIndex.'&action=reloadFromApi&token='.$this->token,
            'desc' => $this->l('Reload Deals')
        );
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getIsset('reloadedFromApi')) {
            $this->confirmations[] = $this->l('Successful Reload from Api');
        }
    }

    public function processReloadFromApi()
    {
        $next = '/deals?expand=deal_definition';
        while ($next != false){
            try {
                $ddResponse = $this->module->core->getApiClient()->get($next);
            } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
                $this->errors[] = $this->l('Reload from Api crashes');
                break;
            }
            if (!$ddResponse instanceof GuzzleHttp\Message\Response) {
                break;
            }
            $ddJson = $ddResponse->json();
            if (!isset($ddJson['deals']) || !is_array($ddJson['deals'])) {
                break;
            }

            foreach ($ddJson['deals'] as $dd) {
                $obj      = IDcutDeal::getByDealId($dd['id']);
                $dd_build = IDcut\Jash\Object\Deal\Deal::build($dd);

                if (!isset($obj->id)) {
                    $obj->deal_id = $dd_build->getId();
                }

                $Date      = strtotime($dd_build->getCreated_at());
                $converted = date("Y-m-d H:i:s", $Date);

                $obj->created_at         = $converted;

                $Date      = strtotime($dd_build->getUpdated_at());
                $converted = date("Y-m-d H:i:s", $Date);

                $obj->updated_at         = $converted;
                $obj->state              = $dd_build->getState();
                $obj->ended              = $dd_build->getEnded();

                $Date      = strtotime($dd_build->getEnd_date());
                $converted = date("Y-m-d H:i:s", $Date);

                $obj->end_date           = $converted;
                $obj->hash_id            = $dd_build->getHash_id();
                $obj->deal_definition_id = $dd['deal_definition']['id'];

                $obj->save();
            }
            $next = false;

            if ($ddResponse->hasHeader('link')) {
                $parsed = \GuzzleHttp\Message\Request::parseHeader($ddResponse, 'Link');
                foreach($parsed as $link){
                    if(isset($link['rel']) && $link['rel']=='next'){
                        $next = trim($link[0],'<>');
                        break;
                    }
                }
            }
            
        }
        if(!count($this->errors)){
            Tools::redirectAdmin(self::$currentIndex.'&reloadedFromApi&token='.$this->token);
        }
    }

    public function renderView()
    {
        $this->loadObject(true);
        $this->tpl_view_vars = array(
            'deal' => $this->object
        );

        return parent::renderView();
    }
}