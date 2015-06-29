<?php

class AdminIDcutDealController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap  = true;
        $this->table      = 'idcut_deal';
        $this->className  = 'IDcutDeal';
        $this->lang       = false;
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your IDcut Deals');

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
        $this->toolbar_title[] = $this->l('IDcut Deals');
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
        try {
            $ddResponse = $this->module->core->getApiClient()->get('/deals?expand=deal_definition');
        } catch (\Exception $e) {
            return false;
        }
        if (!$ddResponse) {
            return false;
        }
        $ddJson = $ddResponse->json();
        if (!isset($ddJson['deals']) || !is_array($ddJson['deals'])) {
            return false;
        }

        foreach ($ddJson['deals'] as $dd) {
            $obj      = IDcutDeal::getByDealId($dd['id']);
            $dd_build = IDcut\Jash\Object\Deal\Deal::build($dd);

            if (!isset($obj->id)) {
                $obj->deal_id = $dd_build->getId();
            }

            $obj->created_at         = $dd_build->getCreated_at();
            $obj->updated_at         = $dd_build->getUpdated_at();
            $obj->state              = $dd_build->getState();
            $obj->ended              = $dd_build->getEnded();
            $obj->end_date           = $dd_build->getEnd_date();
            $obj->hash_id            = $dd_build->getHash_id();
            $obj->deal_definition_id = $dd['deal_definition']['id'];

            $obj->save();
        }

        if ($ddResponse->hasHeader('link') && $link = $dealCreateResponse->getHeader('link')
            && $link->hasLink('next')) {
            $next_link = $link->getLink('next');
            d($next_link);
        }
        ToolsCore::redirectAdmin(self::$currentIndex.'&reloadedFromApi&token='.$this->token);
    }
}