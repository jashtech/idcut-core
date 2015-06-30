<?php

class IDcutDeal extends ObjectModel
{
    /** @var integer */
    public $id;

    /** @var string xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx */
    public $deal_id;

    /** @var date 2015-02-04T00:00:00.000Z */
    public $created_at;

    /** @var date 2015-02-04T00:00:00.000Z */
    public $updated_at;

    /** @var string */
    public $state;

    /** @var boolean */
    public $ended;

    /** @var date 2015-02-04T00:00:00.000Z */
    public $end_date;

    /** @var string xxxxxx-xxxxxx */
    public $hash_id;

    /** @var integer */
    public $deal_definition_id;

    /** @var string url constructed with $deal_id */
    protected $link;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition       = array(
        'table' => 'idcut_deal',
        'primary' => 'id_idcut_deal',
        'multilang' => false,
        'fields' => array(
            'deal_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'required' => true),
            'updated_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'required' => true),
            'state' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName',
                'required' => true, 'size' => 32),
            'ended' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool',
                'required' => true),
            'end_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'required' => true),
            'hash_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'deal_definition_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
        ),
    );
    protected $webserviceParameters = array(
        'objectsNodeName' => 'idcut_deals',
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
            $this->link = '/deals/'.$this->deal_id;
        } else {
            $this->link = null;
        }
        return $this;
    }

    public static function getByDealId($deal_id)
    {
        if (Validate::isReference($deal_id)) {
            $id = Db::getInstance()->getValue('SELECT `id_idcut_deal` as id FROM `'._DB_PREFIX_.'idcut_deal` WHERE deal_id="'.$deal_id.'"');
        } else {
            $id = null;
        }

        return new IDcutDeal($id);
    }

    public static function getDealsByIDcutDealDefinition($deal_definition_id)
    {
        $ret_array = array();
        if (Validate::isReference($deal_definition_id)) {
            $result = Db::getInstance()->ExecuteS('SELECT `id_idcut_deal` as id FROM `'._DB_PREFIX_.'idcut_deal` WHERE deal_definition_id="'.$deal_definition_id.'" ORDER BY created_at DESC');
            foreach ($result as $r) {
                $ret_array[] = new IDcutDeal((int) $r['id']);
            }
        }

        return $ret_array;
    }
}