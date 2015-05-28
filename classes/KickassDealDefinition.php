<?php

class KickassDealDefinition extends ObjectModel
{
    /** @var integer */
    public $id;

    /** @var string xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx */
    public $uuid;

    /** @var date 2015-02-04T00:00:00.000Z */
    public $start_date;

    /** @var date 2015-02-04T00:00:00.000Z */
    public $end_date;

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

    /** @var array array of KickassRange objects */
    public $ranges;

    /** @var string url constructed with $uuid */
    protected $link;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kickass_deal_definition',
        'primary' => 'id_kickass_deal_definition',
        'multilang' => false,
        'fields' => array(
            'uuid' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254, 'copy_post' => false),
            'start_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'required' => true),
            'end_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'required' => true),
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
            $this->ranges = KickassRange::getRangesByKickassDealDefinition($this->id);
            $this->setLink();
        }
    }

    public static function getByUuid($uuid)
    {
        if (Validate::isReference($uuid)) {
            $id = Db::getInstance()->getValue('SELECT `id_kickass_deal_definition` as id FROM `'._DB_PREFIX_.'kickass_deal_definition` WHERE uuid="'.$uuid.'"');
        } else {
            $id = null;
        }

        return new KickassDealDefinition($id);
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