<?php

class AdminIDcutDealDefinitionController extends ModuleAdminController
{
    protected $_helper_list;

    public function __construct()
    {

        $this->bootstrap  = true;
        $this->table      = 'idcut_deal_definition';
        $this->className  = 'IDcutDealDefinition';
        $this->lang       = false;
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->context    = Context::getContext();
        $this->meta_title = $this->l('Your IdealCutter Deal Definition');

        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_idcut_deal_definition' => array('title' => $this->l('ID')),
            'deal_definition_id' => array('title' => $this->l('Deal Definition ID')),
            'ttl' => array('title' => $this->l('Time to join'), 'callback' => 'printTimeForHuman'),
            'locktime' => array('title' => $this->l('Time to return money'), 'callback' => 'printTimeForHuman'),
            'user_max' => array('title' => $this->l('User limit')),
            'min_order_value' => array('title' => $this->l('Minimum order value')),
            'range_type' => array('title' => $this->l('Return Type'), 'callback' => 'printRangeType'),
            'active' => array('title' => $this->l('Current'), 'callback' => 'printCurrent',
                'orderby' => false, 'filter' => false, 'search' => false, 'filter_key' => 'active'),
        );

        parent::__construct();
        if (!$this->module->active)
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCss(_MODULE_DIR_.$this->module->name.'/css/admin/idcut.css');
        $this->addJs(_MODULE_DIR_.$this->module->name.'/js/admin/idcut.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('IdealCutter Deal Definition');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->toolbar_btn['import'] = array(
            'href' => self::$currentIndex.'&action=reloadFromApi&token='.$this->token,
            'desc' => $this->l('Reload Deal Definitions')
        );
    }

    public function initContent()
    {
        $this->token_for_list = Tools::getAdminTokenLite('AdminIDcutDealDefinition');
        $this->_helper_list = new HelperList();
        $this->_helper_list->currentIndex = 'index.php?controller=AdminIDcutDealDefinition';
        $this->_helper_list->identifier = 'id_idcut_deal_definition';
        $this->_helper_list->table = $this->table;
        $this->_helper_list->module = $this->module;
        $this->_helper_list->override_folder = 'i_dcut_deal_definition/';
        parent::initContent();
    }

    public function renderView()
    {
        $this->loadObject(true);
        $this->tpl_view_vars = array(
            'deal_definition' => $this->object
        );

        return parent::renderView();
    }

    public function validateRules($class_name = false)
    {
        if (($error = $this->validateRanges(Tools::getValue('ranges'))) !== true) {
            $this->errors['ranges'] = $error;
        }
        if (Tools::getValue('ranges') !== 0) {
            $this->errors['ranges'] = $this->module->l('Only percent range type is currently allowed');
        }

        parent::validateRules($class_name);
    }

    public function validateRanges($rangesJson)
    {
        if ($rangesJson === false) {
            return $this->module->l('Empty Ranges');
        }
        $rangesArray = json_decode($rangesJson, true);
        if (is_array($rangesArray)) {
            $rge_max = 0;
            foreach ($rangesArray as $rge) {
                $rge = explode('-', $rge);
                if (count($rge) != 2 || !Validate::isUnsignedInt($rge[0]) || !Validate::isUnsignedInt($rge[1])) {
                    return $this->module->l('Ranges are not setted properly');
                } elseif ($rge[1] < $rge_max) {
                    return $this->module->l('Ranges field: more people can not have less reduction');
                }
                $rge_max = $rge[1];
            }
            return true;
        } else {
            return $this->module->l('Ranges are not setted properly');
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getIsset('reloadedFromApi')) {
            $this->confirmations[] = $this->l('Successful Reload from Api');
        }
        if (Tools::getIsset('currentidcut_deal_definition') && $this->id_object) {
            $this->processCurrent();
        }

        $this->id_object = 0;
    }

    public function processReloadFromApi()
    {
        try {
            $ddResponse = $this->module->core->getApiClient()->get('/deal_definitions');
        } catch (\Exception $e) {
            return false;
        }
        if (!$ddResponse) {
            return false;
        }
        $ddJson = $ddResponse->json();
        if (!isset($ddJson['deal_definitions']) || !is_array($ddJson['deal_definitions'])) {
            return false;
        }

        foreach ($ddJson['deal_definitions'] as $dd) {
            $obj      = IDcutDealDefinition::getByDealDefinitionId($dd['id']);
            $dd_build = IDcut\Jash\Object\DealDefinition\DealDefinition::build($dd);

            if (!isset($obj->id)) {
                $obj->deal_definition_id = $dd_build->getId();

                foreach ($dd_build->getRanges() as $range) {
                    $r                          = new IDcutRange();
                    $r->deal_definition_id      = $obj->deal_definition_id;
                    $r->min_participants_number = $range->getMin_participants_number();
                    $r->discount_size           = $range->getDiscount_size();
                    $r->save();
                }
            } else {
                $r_array = array();
                foreach ($dd['ranges'] as $r) {
                    $r_array[(int) $r['min_participants_number']] = (int) $r['discount_size'];
                }

                foreach ($obj->ranges as $range) {
                    if (isset($r_array[$range->min_participants_number])) {
                        $range->discount_size = $r_array[$range->min_participants_number];
                        $range->save();
                        unset($r_array[$range->min_participants_number]);
                    } else {
                        $range->delete();
                    }
                }
                foreach ($r_array as $mpn => $discount) {
                    $r                          = new IDcutRange();
                    $r->deal_definition_id      = $obj->deal_definition_id;
                    $r->min_participants_number = $mpn;
                    $r->discount_size           = $discount;
                    $r->save();
                }
            }

            $obj->active          = $dd_build->getActive();
            $obj->ttl             = $dd_build->getTtl();
            $obj->locktime        = $dd_build->getLocktime();
            $obj->user_max        = $dd_build->getUser_max();
            $obj->min_order_value = $dd_build->getMin_order_value();
            $obj->range_type      = $dd_build->getRange_type() == 'amount' ? true
                    : false;
            $obj->save();
        }

        ToolsCore::redirectAdmin(self::$currentIndex.'&reloadedFromApi&token='.$this->token);
    }

    public function processCurrent()
    {
//        if (!($obj = $this->loadObject(true))) return;
//
//        IDcutDealDefinition::setUnactive($obj->id);
    }

    protected function beforeAdd($object)
    {

        $dd_body = new IDcut\Jash\Object\DealDefinition\DealDefinition();

        $dd_body->setTtl($object->ttl);
        $dd_body->setLocktime($object->locktime);
        $dd_body->setUser_max($object->user_max);
        $dd_body->setMin_order_value($object->min_order_value);
        $dd_body->setRange_type((bool) $object->range_type === false ? 'percent'
                    : 'amount');

        $rangesArray = json_decode($object->ranges, true);
        if (is_array($rangesArray)) {
            foreach ($rangesArray as $rge) {
                $rge = explode('-', $rge);
                $dd_body->addRange(new IDcut\Jash\Object\Range\Range($rge[0],
                    $rge[1]));
            }
        }

        try {
            $ddCreateResponse = $this->module->core->getApiClient()->post('/deal_definitions',
                $dd_body->__toStringForCreate());
        } catch (\Exception $e) {
            return false;
        }

        if ((int) $ddCreateResponse->getStatusCode() !== 201 || !$ddCreateResponse->hasHeader('location')) {
            return false;
        }

        try {
            $location               = $ddCreateResponse->getHeader('location');
            $dealDefinitionResponse = $this->module->core->getApiClient()->get($location);
        } catch (\Exception $e) {
            return false;
        }

        if (!$dealDefinitionResponse) {
            return false;
        }

        $dealDefinitionJson = $dealDefinitionResponse->json();
        if (!isset($dealDefinitionJson['id'])) {
            return false;
        }
        $object->deal_definition_id = $dealDefinitionJson['id'];
        $object->active             = 1;

        foreach ($dd_body->getRanges() as $range) {
            $r                          = new IDcutRange();
            $r->deal_definition_id      = $object->deal_definition_id;
            $r->min_participants_number = $range->getMin_participants_number();
            $r->discount_size           = $range->getDiscount_size();
            if (!$r->save()) {
                return false;
            }
        }

        $old_id = Tools::getValue('old_id');
        if ($old_id && !empty($old_id)) {
            return IDcutDealDefinition::setUnactive($old_id);
        }

        return true;
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) return;

        $ranges_array = array();
        foreach ($obj->ranges as $r) {
            $ranges_array[(int) $r->min_participants_number] = (int) $r->discount_size;
        }
        $old_id = $obj->id;

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Deal Definition'),
                'icon' => 'icon-cog'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'range_type',
                    'value' => 0
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'old_id',
                    'value' => $old_id
                ),
                array(
                    'type' => 'friendly_seconds',
                    'label' => $this->l('Time to join'),
                    'name' => 'ttl',
                    'required' => true
                ),
                array(
                    'type' => 'friendly_seconds',
                    'label' => $this->l('Time to return money'),
                    'name' => 'locktime',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum users'),
                    'name' => 'user_max',
                    'class' => 'col-lg-3',
                    'suffix' => 'participants',
                    'required' => true
                ),
                array(
                    'type' => 'cents_value',
                    'label' => $this->l('Minimum order value'),
                    'name' => 'min_order_value',
                    'suffix' => $this->context->currency->sign,
                    'class' => 'col-lg-3',
                    'required' => true
                ),
//                array(
//                    'type' => 'range_type',
//                    'label' => $this->l('Range type'),
//                    'name' => 'range_type',
//                    'required' => true,
//                    'is_bool' => true,
//                    'values' => array(
//                        array(
//                            'id' => 'range_type_on',
//                            'value' => 1,
//                            'label' => $this->l('Amount')
//                        ),
//                        array(
//                            'id' => 'range_type_off',
//                            'value' => 0,
//                            'label' => $this->l('Percent')
//                        )
//                    ),
//                ),
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
        return isset($types[$t]) ? $types[$t] : $types[0];
    }

    public function printTimeForHuman($t)
    {
        $zero   = new DateTime("@0");
        $offset = new DateTime("@$t");
        $diff   = $zero->diff($offset);
        return sprintf("%02dd %02dh %02dm %02ds", $diff->days, $diff->h,
            $diff->i, $diff->s);
    }

    public function printCurrent($value,$tr)
    {
        return $this->_helper_list->displayEnableLink($this->token_for_list, $tr[$this->_helper_list->identifier], $value, 'current');
    }
}