<?php

class AdminIDcutDealDefinitionController extends ModuleAdminController
{

    public function __construct()
    {

        $this->bootstrap  = true;
        $this->table      = 'idcut_deal_definition';
        $this->className  = 'IDcutDealDefinition';
        $this->lang       = false;
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your IDcut Deal Definition');

        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_idcut_deal_definition' => array('title' => $this->l('ID')),
            'deal_definition_id' => array('title' => $this->l('Deal Definition ID')),
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
        $this->toolbar_title[] = $this->l('IDcut Deal Definition');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function renderView()
    {
        $this->loadObject(true);
        $this->tpl_view_vars = array(
            'deal_definition' => $this->object
        );
        
        return parent::renderView();
    }

    public function postProcess()
    {
        /* Ranges save should be triggered here */
        return parent::postProcess();
    }

    public function renderForm()
    {
            if (!($obj = $this->loadObject(true)))
                    return;

            $ranges_array = array();
            foreach($obj->ranges as $r){
                $ranges_array[(int)$r->min_participants_number] = (int)$r->discount_size;
            }
            $this->fields_form = array(
                    'legend' => array(
                            'title' => $this->l('Deal Definition'),
                            'icon' => 'icon-cog'
                    ),
                    'input' => array(
                            array(
                                    'type' => 'text',
                                    'label' => $this->l('Time to join'),
                                    'name' => 'ttl',
                                    'required' => true
                            ),
                            array(
                                    'type' => 'text',
                                    'label' => $this->l('Time to return money'),
                                    'name' => 'locktime',
                                    'required' => true
                            ),
                            array(
                                    'type' => 'text',
                                    'label' => $this->l('Maximum users'),
                                    'name' => 'user_max',
                                    'required' => true
                            ),
                            array(
                                    'type' => 'text',
                                    'label' => $this->l('Minimum order value'),
                                    'name' => 'min_order_value',
                                    'required' => true
                            ),
                            array(
                                    'type' => 'range_type',
                                    'label' => $this->l('Range type'),
                                    'name' => 'range_type',
                                    'required' => true,
                                    'is_bool' => true,
                                    'values' => array(
                                            array(
                                                    'id' => 'range_type_on',
                                                    'value' => 1,
                                                    'label' => $this->l('Amount')
                                            ),
                                            array(
                                                    'id' => 'range_type_off',
                                                    'value' => 0,
                                                    'label' => $this->l('Percent')
                                            )
                                    ),
                            ),
                            array(
                                    'type' => 'ranges',
                                    'label' => $this->l('Ranges'),
                                    'name' => 'ranges',
                                    'required' => true,
                                    'current_ranges' => $obj->ranges,
                                    'value' => json_encode($ranges_array),
                            ),
                    ),
                    'submit' => array(
                            'title' => $this->l('Save'),
                    )
            );

            return parent::renderForm();
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