<?php

namespace IDcut\Jash\Object\Store;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;

class Store implements JSONObjectInterface
{
    private $created_at         = null;
    private $updated_at         = null;
    private $name               = null;
    private $url                = null;
    private $payment_return_url = null;
    private $hook_url           = null;
    private $join_deal_url      = null;

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getPayment_return_url()
    {
        return $this->payment_return_url;
    }

    public function getHook_url()
    {
        return $this->hook_url;
    }

    public function getJoin_deal_url()
    {
        return $this->join_deal_url;
    }

    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setPayment_return_url($payment_return_url)
    {
        $this->payment_return_url = $payment_return_url;
    }

    public function setHook_url($hook_url)
    {
        $this->hook_url = $hook_url;
    }

    public function setJoin_deal_url($join_deal_url)
    {
        $this->join_deal_url = $join_deal_url;
    }

    static function build($json)
    {
        $store = new Store();

        if ($decoded = json_decode($json, false)) {
            $store->setCreated_at($decoded->created_at);
            $store->setUpdated_at($decoded->updated_at);
            $store->setName($decoded->name);
            $store->setUrl($decoded->url);
            $store->setPayment_return_url($decoded->payment_return_url);
            $store->setHook_url($decoded->hook_url);
            $store->setJoin_deal_url($decoded->join_deal_url);
        }

        return $store;
    }

    public function __toString(){
        $obj = new \stdClass();
        $obj->name = $this->getName();
        $obj->created_at = $this->getCreated_at();
        $obj->updated_at = $this->getUpdated_at();
        $obj->url = $this->getUrl();
        $obj->payment_return_url = $this->getPayment_return_url();
        $obj->hook_url = $this->getHook_url();
        $obj->join_deal_url = $this->getJoin_deal_url();

        return json_encode(array("store"=>$obj));
    }

}