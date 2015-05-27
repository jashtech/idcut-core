<?php

class KickassTransaction extends ObjectModel
{
    /** @var integer */
    public $id;
    public $id_order;
    public $transaction_id;
    public $status;
    public $error_code;
    public $message;
    public $date_edit;
    public $order;

    protected static $available_statuses = array(
        'init' => 0,
        'created' => 1,
        'pending' => 2,
        'completed' => 3,
        'cancelled_by_user' => 4,
        'cancelled_by_payment_gateway' => 5,
        'waiting_payment_gateway' => 6,
        'error' => 7,
    );

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kickass_transaction',
        'primary' => 'id_kickass_transaction',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'transaction_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'error_code' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'message' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml',
                'required' => false),
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
            $id = Db::getInstance()->getValue('SELECT `id_kickass_transaction` as id FROM `'._DB_PREFIX_.'kickass_transaction` WHERE transaction_id="'. $transaction_id.'"');
        }else{
            $id = null;
        }
        
        return new KickassTransaction($id);
    }

    public static function getByOrderId($id_order)
    {
        if(Validate::isUnsignedId($id_order)){
            $id = Db::getInstance()->getValue('SELECT `id_kickass_transaction` as id FROM `'._DB_PREFIX_.'kickass_transaction` WHERE id_order="'. $id_order.'"');
        }else{
            $id = null;
        }

        return new KickassTransaction($id);
    }

    public function setStatus($status)
    {
        if(isset(KickassTransaction::$available_statuses[$status]))
        {
            $this->status = KickassTransaction::$available_statuses[$status];
        }
        else
        {
            $this->status = KickassTransaction::$available_statuses['error'];
        }
        return true;
    }

    public function getStatus()
    {
        $status_name = array_search((int)$this->status, self::$available_statuses, true);
        return $status_name;
    }

}