<?php

class IDcutDeal extends ObjectModel
{
    /** @var integer */
    public $id;
    /** @var string xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx */
    public $deal_id;
    /** @var integer */
    public $id_idcut_deal_definition;
    /** @var date 2015-02-04T00:00:00.000Z */
    public $created_at;
    /** @var string xxxxxx-xxxxxx */
    public $hash_id;

    /** @var string url constructed with $deal_id */
    protected $link;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'idcut_deal',
        'primary' => 'id_idcut_deal',
        'multilang' => false,
        'fields' => array(
            'deal_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'id_idcut_deal_definition' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'required' => true),
            'hash_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
        ),
        'associations' => array(
            'idcut_deal_definition' => array('type' => self::HAS_ONE),
        ),
    );
    protected $webserviceParameters = array(
            'objectsNodeName' => 'idcut_deals',
            'fields' => array(
                    'id_idcut_deal_definition' => array('xlink_resource'=> 'idcut_deal_definitions',),
            ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if (Validate::isLoadedObject($this)) {
            $this->setLink();
        }
    }

    public function getLink()
    {
        return $this->link;
    }

    protected function setLink()
    {
        if (Validate::isReference($this->deal_id)) {
            $this->link = 'https://api.kickass.jash.fr/deals/'.$this->deal_id;
        } else {
            $this->link = null;
        }
        return $this;
    }

    public static function getDealsByIDcutDealDefinition($id_idcut_deal_definition)
    {
        $ret_array = array();
        if(Validate::isUnsignedInt($id_idcut_deal_definition)){
            $result = Db::getInstance()->ExecuteS('SELECT `id_idcut_deal` as id FROM `'._DB_PREFIX_.'idcut_deal` WHERE id_idcut_deal_definition="'. $id_idcut_deal_definition.'" ORDER BY created_at DESC');
            foreach($result as $r){
                $ret_array[]= new IDcutDeal((int)$r['id']);
            }
        }
        
        return $ret_array;
    }

    

}