<?php

class KickassTransactionModuleFrontController extends ModuleFrontController
{
    protected $action_function;
    protected $availableActions = array(
                'view' => 'processView',
                'fail' => 'processFail',
                'error' => 'processError',
                'update' => 'processUpdate'
    );

    public function __construct()
    {
        $action = Tools::getValue('action');
        $this->action = array_key_exists($action, $this->availableActions)?$action:'fail';
        $this->action_function = $this->availableActions[$this->action];
        $this->origin_action = $action;
        parent::__construct();

    }
    
    // this calls proper action
    public function postProcess()
    {
        $transaction_id = $this->getTransactionId();
        $KickassTransaction = KickassTransaction::getByTransactionId($transaction_id);
        if(!Validate::isLoadedObject($KickassTransaction)){
            if($this->action == 'update'){
                $this->errors[] = Tools::displayError('Transaction id is invalid.');
                return false;
            }else{
                $this->action = 'error';
                $this->action_function = $this->availableActions[$this->action];
                $KickassTransaction->transaction_id = $transaction_id;
            }
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

    public function processError($KickassTransaction)
    {
        $customer = $this->context->customer;
        $KickassTransaction->order->id_customer = $customer->id;
        $KickassTransaction->id_order = 0;
        $KickassTransaction->order->id = 0;
        $KickassTransaction->order->id_cart = 0;

        $this->processFail($KickassTransaction);
    }
    public function processFail(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);

        $KickassTransaction->setStatus('error');
        $KickassTransaction->error_code = $this->getErrorCode();
        $KickassTransaction->message = $this->getMessage();
        $KickassTransaction->date_edit = date('Y-m-d H:i:s');
        $KickassTransaction->save();
d($KickassTransaction);
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $KickassTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$KickassTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processUpdate(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);
        $status = $this->getStatusForUpdate();

        switch($status){
            case 'completed':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_PAYMENT'), $KickassTransaction->order, null);
                break;
            case 'pending':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_KICKASS_PENDING'), $KickassTransaction->order, null);
                break;
            case 'created':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_CANCELED'), $KickassTransaction->order, null);
                break;
            default:
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_ERROR'), $KickassTransaction->order, null);
                break;
        }
        $KickassTransaction->setStatus($status);
        $KickassTransaction->error_code = $this->getErrorCodeForUpdate();
        $KickassTransaction->message = $this->getMessageForUpdate();
        $KickassTransaction->date_edit = date('Y-m-d H:i:s');
        $KickassTransaction->save();
        echo 'transaction updated';
        exit;
    }

    protected function getStatus()
    {
        return Tools::getValue('status', 'error');
    }
    protected function getStatusForUpdate()
    {
        return Tools::getValue('status', 'error');
    }

    protected function getErrorCode()
    {
        return Tools::getValue('error_code', null);
    }
    protected function getErrorCodeForUpdate()
    {
        return Tools::getValue('error_code', null);
    }

    protected function getMessage()
    {
        return Tools::getValue('message', null);
    }
    protected function getMessageForUpdate()
    {
        return Tools::getValue('message', null);
    }

    protected function setOrderStatus($new_id_order_state, $order, $message = null)
    {
        // just copy pasted from AdminOrdersController - shuld be rebuilded to do somtheing similar
        $order_state = new OrderState($new_id_order_state);

        if (!Validate::isLoadedObject($order_state)){
                $this->errors[] = Tools::displayError('The new order status is invalid.');
                $this->errors[] = Tools::displayError('Trying order status: '.$new_id_order_state);
        }
        else
        {
                $current_order_state = $order->getCurrentOrderState();
                if ($current_order_state->id != $order_state->id)
                {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = 0;

                        $use_existings_payment = false;
                        if (!$order->hasInvoice())
                                $use_existings_payment = true;
                        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                        $carrier = new Carrier($order->id_carrier, $order->id_lang);
                        $templateVars = array();
                        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                                $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

                        // Save all changes
                        if ($history->addWithemail(true, $templateVars))
                        {
                                // synchronizes quantities if needed..
                                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
                                {
                                        foreach ($order->getProducts() as $product)
                                        {
                                                if (StockAvailable::dependsOnStock($product['product_id']))
                                                        StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                                        }
                                }

                                return true;
                        }
                        $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
                }
                else
                        return true;

                return false;
        }
    }

    protected function getTransactionId()
    {
        /* Example
         * for '492131f6-7556-4735-a5c3-89e5c115cbf4'
         */
        return Tools::getValue('transaction_id', '492131f6-7556-4735-a5c3-89e5c115cbf4');
    }
}