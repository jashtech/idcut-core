<?php

class IDcutDealDefinition extends ObjectModel
{
    /** @var integer */
    public $id;

    /** @var string xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx */
    public $uuid;

    /** @var int seconds */
    public $ttl;

    /** @var int seconds */
    public $locktime;

    /** @var int */
    public $user_max;

    /** @var int by default in EUR cents */
    public $min_order_value;

    /** @var bool false-percent true-amount */
    public $range_type;

    /** @var array array of IDcutRange objects */
    public $ranges;

    /** @var string url constructed with $uuid */
    protected $link;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'idcut_deal_definition',
        'primary' => 'id_idcut_deal_definition',
        'multilang' => false,
        'fields' => array(
            'uuid' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254, 'copy_post' => false),
            'ttl' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'locktime' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'user_max' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'min_order_value' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'range_type' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool',
                'required' => true),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if (Validate::isLoadedObject($this)) {
            $this->ranges = IDcutRange::getRangesByIDcutDealDefinition($this->id);
            $this->setLink();
        }
    }

    public static function getByUuid($uuid)
    {
        if (Validate::isReference($uuid)) {
            $id = Db::getInstance()->getValue('SELECT `id_idcut_deal_definition` as id FROM `'._DB_PREFIX_.'idcut_deal_definition` WHERE uuid="'.$uuid.'"');
        } else {
            $id = null;
        }

        return new IDcutDealDefinition($id);
    }

    public function getLink()
    {
        return $this->link;
    }

    protected function setLink()
    {
        if (Validate::isReference($this->uuid)) {
            $this->link = 'https://api.kickass.jash.fr/deal_definitions/'.$this->uuid;
        } else {
            $this->link = null;
        }
        return $this;
    }
    
    public function formatTtl(){
        return $this->formatSeconds((int)$this->ttl);
    }

    public function formatLocktime(){
        return $this->formatSeconds((int)$this->locktime);
    }

    protected function formatSeconds($seconds){
        $zero    = new DateTime("@0");
        $offset  = new DateTime("@$seconds");
        $diff    = $zero->diff($offset);
        return sprintf("%02dd %02dh %02dm %02ds", $diff->days, $diff->h, $diff->i, $diff->s);
    }
}