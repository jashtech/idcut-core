<?php

class KickassTransactionModuleFrontController extends ModuleFrontController
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
        $KickassTransaction = KickassTransaction::getByTransactionId($transaction_id);
        if(!Validate::isLoadedObject($KickassTransaction)){
            $this->action = 'error';
            $this->action_function = $this->availableActions[$this->action];
            $KickassTransaction->transaction_id = $transaction_id;
        }
        if (is_callable($this->{$this->action_function}($KickassTransaction))){
            $this->{$this->action_function}($KickassTransaction);
        }
    }

    //this change status for transaction and redirect to end order-confirmation page
    public function processView(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);
        $status = $this->getStatus();

        $KickassTransaction->setStatus($status);
        $KickassTransaction->error_code = $this->getErrorCode();
        $KickassTransaction->message = $this->getMessage();
        $KickassTransaction->date_edit = date('Y-m-d H:i:s');
        $KickassTransaction->save();

        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $KickassTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$KickassTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processError(KickassTransaction $KickassTransaction)
    {
        $customer = $this->context->customer;
        $KickassTransaction->order->id_customer = $customer->id;
        $KickassTransaction->id_order = 0;
        $KickassTransaction->order->id = 0;
        $KickassTransaction->order->id_cart = 0;

        $this->processView($KickassTransaction);
    }

    protected function getStatus()
    {
        return Tools::getValue('status', 'error');
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