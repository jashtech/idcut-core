<?php

class KickassTransaction extends ObjectModel
{
    /** @var integer */
    public $id;
    public $id_order;
    public $transaction_id;
    public $status;
    public $date_edit;
    public $order;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kickasstransaction',
        'primary' => 'id_kickasstransaction',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'transaction_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_edit' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
        'associations' => array(
            'order' => array('type' => self::HAS_ONE),
        ),
    );
    
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->order = new Order((int)$this->id_order, $id_lang, $id_shop);
    }

    public static function getByTransactionId($transaction_id)
    {
        if(Validate::isReference($transaction_id)){
            $id = Db::getInstance()->getValue('SELECT `id_kickasstransaction` as id FROM `'._DB_PREFIX_.'kickasstransaction` WHERE transaction_id="'. $transaction_id.'"');
        }else{
            $id = null;
        }
        
        return new KickassTransaction($id);
    }

}