<?php

class IDcutDeal_With_ItModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        $this->action = 'view';
        parent::__construct();
    }
    
    // this calls proper action
    public function postProcess()
    {
        echo 'I want to deal with You';
    }

}