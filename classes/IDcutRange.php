<?php

class IDcutRange extends ObjectModel
{
    /** @var integer */
    public $id;
    /** @var int */
    public $id_idcut_deal_definition;
    /** @var int min participants to trigger */
    public $min_participants_number;
    /** @var int mixed cents or percent */
    public $discount_size;
    

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'idcut_range',
        'primary' => 'id_idcut_range',
        'multilang' => false,
        'fields' => array(
            'id_idcut_deal_definition' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'min_participants_number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'discount_size' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
        ),
        'associations' => array(
            'idcut_deal_definition' => array('type' => self::HAS_ONE),
        ),
    );
    protected $webserviceParameters = array(
            'objectsNodeName' => 'idcut_ranges',
            'fields' => array(
                    'id_idcut_deal_definition' => array('xlink_resource'=> 'idcut_deal_definitions',),
            ),
    );

    public static function getRangesByIDcutDealDefinition($id_idcut_deal_definition)
    {
        $ret_array = array();
        if(Validate::isUnsignedInt($id_idcut_deal_definition)){
            $result = Db::getInstance()->ExecuteS('SELECT `id_idcut_range` as id FROM `'._DB_PREFIX_.'idcut_range` WHERE id_idcut_deal_definition="'. $id_idcut_deal_definition.'" ORDER BY min_participants_number');
            foreach($result as $r){
                $ret_array[]= new IDcutRange((int)$r['id']);
            }
        }
        
        return $ret_array;
    }

    

}