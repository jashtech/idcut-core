<?php

class IDcutDealDefinition extends ObjectModel
{
    /** @var integer */
    public $id;

    /** @var string xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx */
    public $deal_definition_id;

    /** @var bool */
    public $active;

    /** @var int seconds */
    public $ttl;

    /** @var int seconds */
    public $locktime = 1209600;

    /** @var int */
    public $user_max;

    /** @var int by default in EUR cents */
    public $min_order_value;

    /** @var bool false-percent true-amount */
    public $range_type;

    /** @var array array of IDcutRange objects */
    public $ranges = array();

    /** @var string url constructed with $deal_definition_id */
    protected $link;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'idcut_deal_definition',
        'primary' => 'id_idcut_deal_definition',
        'multilang' => false,
        'fields' => array(
            'deal_definition_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254, 'copy_post' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool',
                'required' => true),
            'ttl' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'locktime' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true, 'copy_post' => false),
            'user_max' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'min_order_value' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'range_type' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if (Validate::isLoadedObject($this)) {
            $this->ranges = IDcutRange::getRangesByIDcutDealDefinition($this->deal_definition_id);
            $this->setLink();
        }
    }

    public static function getByDealDefinitionId($deal_definition_id)
    {
        if (Validate::isReference($deal_definition_id)) {
            $id = Db::getInstance()->getValue('SELECT `id_idcut_deal_definition` as id FROM `'._DB_PREFIX_.'idcut_deal_definition` WHERE deal_definition_id="'.$deal_definition_id.'"');
        } else {
            $id = null;
        }

        return new IDcutDealDefinition($id);
    }

    public static function getActive()
    {
        $id = Db::getInstance()->getValue('SELECT `id_idcut_deal_definition` as id FROM `'._DB_PREFIX_.'idcut_deal_definition` WHERE active=1');
        return new IDcutDealDefinition($id);
    }

    public static function setUnactive($id)
    {
        if(!Validate::isUnsignedInt($id)){
            return false;
        }
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'idcut_deal_definition` SET  active=0 WHERE `id_idcut_deal_definition`='.(int)$id);
    }

    public function getLink()
    {
        return $this->link;
    }

    protected function setLink()
    {
        if (Validate::isReference($this->deal_definition_id)) {
            $this->link = '/deal_definitions/'.$this->deal_definition_id;
        } else {
            $this->link = null;
        }
        return $this;
    }

    public function formatTtl()
    {
        return $this->formatSeconds((int) $this->ttl);
    }

    public function formatLocktime()
    {
        return $this->formatSeconds((int) $this->locktime);
    }

    protected function formatSeconds($seconds)
    {
        $zero   = new DateTime("@0");
        $offset = new DateTime("@$seconds");
        $diff   = $zero->diff($offset);
        return sprintf("%02dd %02dh %02dm %02ds", $diff->days, $diff->h,
            $diff->i, $diff->s);
    }
}