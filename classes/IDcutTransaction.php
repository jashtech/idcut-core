<?php

class IDcutTransaction extends ObjectModel
{
    /** @var integer */
    public $id;
    public $id_order;
    public $id_cart;
    public $transaction_id;
    public $deal_id;
    public $status;
    public $title;
    public $amount;
    public $amount_cents;
    public $amount_currency;
    public $error_code;
    public $message;
    public $created_at;
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
        'table' => 'idcut_transaction',
        'primary' => 'id_idcut_transaction',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                'required' => true),
            'transaction_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'size' => 254),
            'deal_id' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 254),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName',
                'size' => 254),
            'amount' => array('type' => self::TYPE_STRING, 'validate' => 'isReference',
                'required' => true, 'size' => 32),
            'amount_cents' => array('type' => self::TYPE_INT, 'validate' => 'isInt',
                'required' => true),
            'amount_currency' => array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode',
                'required' => true, 'size' => 3),
            'error_code' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'message' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml',
                'required' => false),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'copy_post' => false),
            'date_edit' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'copy_post' => false),
        ),
        'associations' => array(
            'order' => array('type' => self::HAS_ONE),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->order = new Order((int) $this->id_order, $id_lang, $id_shop);
    }

    public static function getByTransactionId($transaction_id)
    {
        if (Validate::isReference($transaction_id)) {
            $id = Db::getInstance()->getValue('SELECT `id_idcut_transaction` as id FROM `'._DB_PREFIX_.'idcut_transaction` WHERE transaction_id="'.$transaction_id.'"');
        } else {
            $id = null;
        }

        return new IDcutTransaction($id);
    }

    public static function getByOrderId($id_order)
    {
        if (Validate::isUnsignedId($id_order)) {
            $id = Db::getInstance()->getValue('SELECT `id_idcut_transaction` as id FROM `'._DB_PREFIX_.'idcut_transaction` WHERE id_order="'.$id_order.'"');
        } else {
            $id = null;
        }

        return new IDcutTransaction($id);
    }

    public static function getByCartId($id_cart)
    {
        if (Validate::isUnsignedId($id_cart)) {
            $id = Db::getInstance()->getValue('SELECT `id_idcut_transaction` as id FROM `'._DB_PREFIX_.'idcut_transaction` WHERE id_cart="'.$id_cart.'"');
        } else {
            $id = null;
        }

        return new IDcutTransaction($id);
    }

    public function setStatus($status)
    {
        if (isset(IDcutTransaction::$available_statuses[$status])) {
            $this->status = IDcutTransaction::$available_statuses[$status];
        } else {
            $this->status = IDcutTransaction::$available_statuses['pending'];
        }
        return true;
    }

    public function setAmount_cents_AND_currency($total, Currency $currency)
    {
        $this->amount_cents    = (int) ($total * 100);
        $this->amount_currency = $currency->iso_code;

        return $this;
    }

    public function getStatus()
    {
        $status_name = array_search((int) $this->status,
            self::$available_statuses, true);
        return $status_name;
    }
}