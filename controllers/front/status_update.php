<?php

class KickassStatusUpdateModuleFrontController extends ModuleFrontController
{
    protected $action_function;
    protected $availableActions = array(
                'view' => 'processUpdate',
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
                echo $this->l('Transaction id is invalid');
                exit;
        }
        if (is_callable($this->{$this->action_function}($KickassTransaction))){
            $this->{$this->action_function}($KickassTransaction);
        }

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
            case 'waiting_payment_gateway':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_KICKASS_PENDING'), $KickassTransaction->order, null);
                break;
            case 'created':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_KICKASS_PENDING'), $KickassTransaction->order, null);
                break;
            case 'cancelled_by_user':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_CANCELED'), $KickassTransaction->order, null);
                break;
            case 'cancelled_by_payment_gateway':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_CANCELED'), $KickassTransaction->order, null);
                break;
            case 'error':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_ERROR'), $KickassTransaction->order, null);
                break;
            default:
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_KICKASS_PENDING'), $KickassTransaction->order, null);
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

    protected function getStatusForUpdate()
    {
        return Tools::getValue('status', 'pending');
    }

    protected function getErrorCodeForUpdate()
    {
        return Tools::getValue('error_code', null);
    }

    protected function getMessageForUpdate()
    {
        return Tools::getValue('message', null);
    }

    /* now this is not used - it will be moved */
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
        return Tools::getValue('transaction_id');
    }
}