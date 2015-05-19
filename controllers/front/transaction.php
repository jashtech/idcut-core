<?php

class KickassTransactionModuleFrontController extends ModuleFrontController
{
    protected $action_function;

    public function __construct()
    {
        $availableActions = array(
            'view' => 'processView',
            'fail' => 'processFail',
            'update' => 'processUpdate'
        );
        $action = Tools::getValue('action');
        $this->action = array_key_exists($action, $availableActions)?$action:'fail';
        $this->action_function = $availableActions[$this->action];
        $this->origin_action = $action;
        parent::__construct();

    }
    
    // this calls proper action
    public function postProcess()
    {
        $transaction_id = $this->getTransactionId();
        $KickassTransaction = KickassTransaction::getByTransactionId($transaction_id);

        if (is_callable($this->{$this->action_function}($KickassTransaction))){
            $this->{$this->action_function}($KickassTransaction);
        }
    }

    //this change status for transaction and redirect to end order-confirmation page
    public function processView(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);
        $status = $this->getStatus();

        switch($status){
            case 'completed':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_PAYMENT'), $KickassTransaction->order, null);
//                $this->module->validateOrder((int) $KickassTransaction->order->id_cart,
//                    Configuration::get('PS_OS_PAYMENT'), $total,
//                    $this->module->displayName, $this->l('Payment accepted.'), $mailVars, (int) $currency->id,
//                    false, $customer->secure_key);
                break;
            case 'pending':
                $orderStatusUpdate = true;
                //PS_OS_WS_PAYMENT
                break;
            case 'created':
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_CANCELED'), $KickassTransaction->order, null);
//                $this->module->validateOrder((int) $KickassTransaction->order->id_cart,
//                    Configuration::get('PS_OS_CANCELED'), $total,
//                    $this->module->displayName, $this->l('Payment cancelled.'), $mailVars, (int) $currency->id,
//                    false, $customer->secure_key);
                break;
            default:
                $orderStatusUpdate = $this->setOrderStatus(Configuration::get('PS_OS_ERROR'), $KickassTransaction->order, null);
//                $this->module->validateOrder((int) $KickassTransaction->order->id_cart,
//                    Configuration::get('PS_OS_ERROR'), $total,
//                    $this->module->displayName, $this->l('Error while paying.'), $mailVars, (int) $currency->id,
//                    false, $customer->secure_key);
                break;
        }
        $KickassTransaction->status = $status;
        $KickassTransaction->save();

        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $KickassTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$KickassTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processFail(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);
        d('Kickass returned to transaction processFail');
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $KickassTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$KickassTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processUpdate(KickassTransaction $KickassTransaction)
    {
        // we will update status and inform user by e-mail
        d('Kickass returned to transaction processUpdate');
    }

    protected function getStatus()
    {
        return Tools::getValue('status', 'completed');
    }

    protected function setOrderStatus($new_id_order_state, $order, $message = null)
    {
        // just copy pasted from AdminOrdersController - shuld be rebuilded to do somtheing similar
        $order_state = new OrderState($new_id_order_state);

        if (!Validate::isLoadedObject($order_state))
                $this->errors[] = Tools::displayError('The new order status is invalid.');
        else
        {
                $current_order_state = $order->getCurrentOrderState();
                if ($current_order_state->id != $order_state->id)
                {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int)$this->context->employee->id;

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
                        $this->errors[] = Tools::displayError('The order has already been assigned this status.');

                return false;
        }
    }

    protected function getTransactionId()
    {
        /* Example
         * for '492131f6-7556-4735-a5c3-89e5c115cbf4'
         */
        return '492131f6-7556-4735-a5c3-89e5c115cbf4';
    }
}