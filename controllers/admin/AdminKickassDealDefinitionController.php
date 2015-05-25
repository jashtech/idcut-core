<?php

class AdminKickassDealDefinitionController extends ModuleAdminController
{

    public function __construct()
    {

        $this->bootstrap  = true;
        $this->table      = 'kickass_deal_definition';
        $this->className  = 'KickassDealDefinition';
        $this->lang       = false;
        $this->addRowAction('view');
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your Kickass Deal Definition');

        $this->_orderBy = 'start_date';
        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_kickass_deal_definition' => array('title' => $this->l('ID')),
            'uuid' => array('title' => $this->l('Deal Definition ID')),
            'start_date' => array('title' => $this->l('Starts')),
            'end_date' => array('title' => $this->l('Ends')),
            'ttl' => array('title' => $this->l('Time to join'), 'callback' => 'printTimeForHuman'),
            'locktime' => array('title' => $this->l('Time to return money'), 'callback' => 'printTimeForHuman'),
            'user_max' => array('title' => $this->l('User limit')),
            'min_order_value' => array('title' => $this->l('Minimum order value')),
            'range_type' => array('title' => $this->l('Return Type'), 'callback' => 'printRangeType'),
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
        $this->toolbar_title[] = $this->l('Kickass Deal Definition');
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
            'deal_definition' => $this->object
        );
        
        return parent::renderView();
    }

    public function printRangeType($t)
    {
        $types = array(0 => $this->l('Percent'), 1 => $this->l('Amount'));
        return isset($types[$t])?$types[$t]:$types[0];
    }

    public function printTimeForHuman($t)
    {
        $zero    = new DateTime("@0");
        $offset  = new DateTime("@$t");
        $diff    = $zero->diff($offset);
        return sprintf("%02dd %02dh %02dm %02ds", $diff->days, $diff->h, $diff->i, $diff->s);
    }

}