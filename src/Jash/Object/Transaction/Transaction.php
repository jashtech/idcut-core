<?php

namespace IDcut\Jash\Object\Transaction;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;

class Transaction implements JSONObjectInterface
{

    private $id                 = null;
    private $created_at         = null;
    private $title               = null;
    private $status                = null;
    private $amount = null;
    private $amount_cents           = null;
    private $amount_currency      = null;
    private $link      = null;
    private $confirm_payment_link      = null;
    private $deal_id      = null;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getAmount_cents()
    {
        return $this->amount_cents;
    }

    public function getAmount_currency()
    {
        return $this->amount_currency;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getDeal_id()
    {
        return $this->deal_id;
    }

    public function getConfirm_payment_link()
    {
        return $this->confirm_payment_link;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setAmount_cents($amount_cents)
    {
        $this->amount_cents = $amount_cents;
    }

    public function setAmount_currency($amount_currency)
    {
        $this->amount_currency = $amount_currency;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function setDeal_id($deal_id)
    {
        $this->deal_id = $deal_id;
    }

    public function setConfirm_payment_link($confirm_payment_link)
    {
        $this->confirm_payment_link = $confirm_payment_link;
    }

    static function build(Array $input)
    {
        $transaction = new Transaction();

        if ($input) {
            $transaction->setId($input['id']);
            $transaction->setCreated_at($input['created_at']);
            $transaction->setTitle($input['title']);
            $transaction->setStatus($input['status']);
            $transaction->setAmount($input['amount']);
            $transaction->setAmount_cents($input['amount_cents']);
            $transaction->setAmount_currency($input['amount_currency']);
            $transaction->setLink($input['link']);
            if(isset($input['confirm_payment_link'])){
                $transaction->setConfirm_payment_link($input['confirm_payment_link']);
            }
        }
        return $transaction;
    }

    public function __toString(){
        $transaction = array();
        $transaction['id'] = $this->getId();
        $transaction['created_at']  = $this->getCreated_at();
        $transaction['title']  = $this->getTitle();
        $transaction['status']  = $this->getStatus();
        $transaction['amount']  = $this->getAmount();
        $transaction['amount_cents']  = $this->getAmount_cents();
        $transaction['amount_currency']  = $this->getAmount_currency();
        $transaction['link']  = $this->getLink();
        $transaction['confirm_payment_link']  = $this->getConfirm_payment_link();

        $transaction = array_filter($transaction);
        return json_encode(array("transaction"=>$transaction) , JSON_UNESCAPED_SLASHES );
    }
    
    public function __toStringForCreate(){
        $transaction = array();

        $transaction['amount_cents']  = $this->getAmount_cents();
        $transaction['amount_currency']  = $this->getAmount_currency();
        $transaction['title']  = $this->getTitle();
        $transaction['deal_id']  = $this->getDeal_id();

        if(empty($transaction['amount_currency'])){
            unset($transaction['amount_currency']);
        }
        if(empty($transaction['deal_id'])){
            unset($transaction['deal_id']);
        }

        $transaction = array_filter($transaction);
        return json_encode(array("transaction"=>$transaction) , JSON_UNESCAPED_SLASHES );
    }

}