<?php

namespace IDcut\Jash\Object\Store;

use IDcut\Jash\Object\JSONObjectInterface as JSONObjectInterface;

class Store implements JSONObjectInterface
{
    private $active             = null;
    private $created_at         = null;
    private $updated_at         = null;
    private $name               = null;
    private $url                = null;
    private $payment_return_url = null;
    private $hook_url           = null;
    private $join_deal_url      = null;

    public function getActive()
    {
        return $this->active;
    }
    
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

    public function setActive($active)
    {
        $this->active = $active;
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

    static function build(Array $input)
    {
        $store = new Store();

        if ($input) {
            $store->setActive($input['active']);
            $store->setCreated_at($input['created_at']);
            $store->setUpdated_at($input['updated_at']);
            $store->setName($input['name']);
            $store->setUrl($input['url']);
            $store->setPayment_return_url($input['payment_return_url']);
            $store->setHook_url($input['hook_url']);
            $store->setJoin_deal_url($input['join_deal_url']);
        }
        return $store;
    }

    public function __toString(){
        $store = array();
        $store['name'] = $this->getName();
        $store['created_at']  = $this->getCreated_at();
        $store['updated_at']  = $this->getUpdated_at();
        $store['url']  = $this->getUrl();
        $store['payment_return_url']  = $this->getPayment_return_url();
        $store['hook_url']  = $this->getHook_url();
        $store['join_deal_url'] = $this->getJoin_deal_url();

        $store = array_filter($store);
        return json_encode(array("store"=>$store) , JSON_UNESCAPED_SLASHES );
    }

    public function __toStringUpdateUrls(){
        $store = array();
        $store['payment_return_url']  = $this->getPayment_return_url();
        $store['hook_url']  = $this->getHook_url();
        $store['join_deal_url'] = $this->getJoin_deal_url();

        $store = array_filter($store);
        return json_encode(array("store"=>$store) , JSON_UNESCAPED_SLASHES );
    }

}