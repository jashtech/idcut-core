<?php

class IDcutRange extends ObjectModel
{
    /** @var integer */
    public $id;

    /** @var string */
    public $deal_definition_id;

    /** @var int min participants to trigger */
    public $min_participants_number;

    /** @var int mixed cents or percent */
    public $discount_size;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition       = array(
        'table' => 'idcut_range',
        'primary' => 'id_idcut_range',
        'multilang' => false,
        'fields' => array(
            'deal_definition_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'min_participants_number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'discount_size' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
        ),
    );
    protected $webserviceParameters = array(
        'objectsNodeName' => 'idcut_ranges',
    );

    public static function getRangesByIDcutDealDefinition($deal_definition_id)
    {
        $ret_array = array();
        if (Validate::isReference($deal_definition_id)) {
            $result = Db::getInstance()->ExecuteS('SELECT `id_idcut_range` as id FROM `'._DB_PREFIX_.'idcut_range` WHERE deal_definition_id="'.$deal_definition_id.'" ORDER BY min_participants_number');
            foreach ($result as $r) {
                $ret_array[] = new IDcutRange((int) $r['id']);
            }
        }

        return $ret_array;
    }
}