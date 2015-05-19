<?php

class KickassTransactionModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $availableActions = array(
            'view' => 'processView',
            'fail' => 'processFail',
            'update' => 'processUpdate'
        );
        $action = Tools::getValue('action');
        $this->action = array_key_exists($action, $availableActions)?$action:'fail';
        $this->origin_action = $action;
        parent::__construct();

    }
    // this will change status for transaction and redirect to end order-confirmation page with status OK or NOT

    

    public function postProcess()
    {
        d('Kickass returned to transaction action: '.$this->origin_action.' : converted to: '.$this->action.' :');
        $transaction_id = Tools::getValue('transaction_id');

        $KickassTransaction = KickassTransaction::getByTransactionId($transaction_id);

    }

    public function processView(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);
        $status = $this->getStatus();
        switch($status){
            case 'completed':
                $this->module->validateOrder((int) $cart->id,
                    Configuration::get('PS_OS_PAYMENT'), $total,
                    $this->module->displayName, $this->l('Payment accepted.'), $mailVars, (int) $currency->id,
                    false, $customer->secure_key);
                break;
            case 'pending':
                break;
            case 'created':
                $this->module->validateOrder((int) $cart->id,
                    Configuration::get('PS_OS_CANCELED'), $total,
                    $this->module->displayName, $this->l('Payment cancelled.'), $mailVars, (int) $currency->id,
                    false, $customer->secure_key);
                break;
            default:
                $this->module->validateOrder((int) $cart->id,
                    Configuration::get('PS_OS_ERROR'), $total,
                    $this->module->displayName, $this->l('Error while paying.'), $mailVars, (int) $currency->id,
                    false, $customer->secure_key);
                break;
        }

        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $KickassTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$KickassTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processFail(KickassTransaction $KickassTransaction)
    {
        $customer = new Customer((int)$KickassTransaction->order->id_customer);
        d('Kickass returned to transaction fail');
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int) $KickassTransaction->order->id_cart.'&id_module='.(int) $this->module->id.'&id_order='.$KickassTransaction->order->id.'&key='.$customer->secure_key);
    }

    public function processUpdate(KickassTransaction $KickassTransaction)
    {
        // we will update status and inform user by e-mail
        d('Kickass returned to transaction update');
    }

    protected function getStatus()
    {
        return Tools::getValue('status', 'completed');
    }
}