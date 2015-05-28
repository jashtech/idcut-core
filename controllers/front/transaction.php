<?php

class IDcutTransactionModuleFrontController extends ModuleFrontController
{
    protected $action_function;
    protected $availableActions = array(
                'view' => 'processView',
                'error' => 'processError',
    );

    public function __construct()
    {
        $this->action = 'view';
        $this->action_function = $this->availableActions[$this->action];
        parent::__construct();
    }
    
    // this calls proper action
    public function postProcess()
    {
        $transaction_id = $this->getTransactionId();
        $IDcutTransaction = IDcutTransaction::getByTransactionId($transaction_id);
        if(!Validate::isLoadedObject($IDcutTransaction)){
            $this->action = 'error';
            $this->action_function = $this->availableActions[$this->action];
            $IDcutTransaction->transaction_id = $transaction_id;
        }
        if (is_callable($this->{$this->action_function}($IDcutTransaction))){
            $this->{$this->action_function}($IDcutTransaction);
        }
    }

    //this change status for transaction and redirect to end order-confirmation page
    public function processView(IDcutTransaction $IDcutTransaction)
    {
        $customer = new Customer((int)$IDcutTransaction->order->id_customer);
        $status = $this->getStatus();

        $IDcutTransaction->setStatus($status);
        $IDcutTransaction->error_code = $this->getErrorCode();
        $IDcutTransaction->message = $this->getMessage();
        $IDcutTransaction->date_edit = date('Y-m-d H:i:s');
        $IDcutTransaction->save();

        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $IDcutTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$IDcutTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processError(IDcutTransaction $IDcutTransaction)
    {
        $customer = $this->context->customer;
        $IDcutTransaction->order->id_customer = $customer->id;
        $IDcutTransaction->id_order = 0;
        $IDcutTransaction->order->id = 0;
        $IDcutTransaction->order->id_cart = 0;

        $this->processView($IDcutTransaction);
    }

    protected function getStatus()
    {
        return Tools::getValue('status', 'pending');
    }

    protected function getErrorCode()
    {
        return Tools::getValue('error_code', null);
    }

    protected function getMessage()
    {
        return Tools::getValue('message', null);
    }

    protected function getTransactionId()
    {
        /* Example
         * for '492131f6-7556-4735-a5c3-89e5c115cbf4'
         */
        return Tools::getValue('transaction_id', '492131f6-7556-4735-a5c3-89e5c115cbf4');
    }
}