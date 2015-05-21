<?php

class KickassValidationModuleFrontController extends ModuleFrontController
{
    protected function getCurrentDealDefinition()
    {
        /*
         * GET http://api.kickass.jash.fr/deal_definitions/<uuid>
         * 
         * example for deal d9d03443-befe-480d-866e-03154e1f7670
         */
        //        $response = $this->module->core->getApiClient()->getCurrentDealDefinition();
        //        if($response instanceof GuzzleHttp\Message\Response){
        //            $r = var_export($response , true);
        //        }
        $json_response ='{"id":"d9d03443-befe-480d-866e-03154e1f7670","start_date":"2015-02-04T00:00:00.000Z","end_date":"2015-09-20T00:00:00.000Z","ttl":242332,"locktime":4234354,"user_max":5,"min_order_value":636,"range_type":"percent","ranges":[{"min_participants_number":4,"discount_size":5}],"link":"https://api.kickass.jash.fr/deal_definitions/d9d03443-befe-480d-866e-03154e1f7670"}';
        return json_decode($json_response);
    }
    
    protected function createTransaction($deal)
    {
        /*
            POST http://api.kickass.jash.fr/deal_definitions/<deal_definition_id>/transactions
            {
                "transaction":{
                    "amount_cents": "1000",
                    "title": "order payment"
                }
            }
         *
         * example for deal d9d03443-befe-480d-866e-03154e1f7670
         * returned transaction_id 492131f6-7556-4735-a5c3-89e5c115cbf4
         */
        //        $response = $this->module->core->getApiClient()->createTransaction();
        //        if($response instanceof GuzzleHttp\Message\Response){
        //            $r = var_export($response , true);
        //        }

        $request_link = $deal->link.'/transactions';

        $transaction_id = $this->getTransactionId($request_link);

        $transaction = KickassTransaction::getByTransactionId($transaction_id);
        if(!isset($transaction->id)){
            $transaction->transaction_id = $transaction_id;
            $transaction->id_order = $this->module->currentOrder;
            $transaction->setStatus('init');
            $transaction->date_edit = date('Y-m-d H:i:s');
            $transaction->save();
        }

        return $transaction;
    }

    protected function getTransactionId($request_link)
    {
        /* Example
         * for '492131f6-7556-4735-a5c3-89e5c115cbf4'
         */
        return '492131f6-7556-4735-a5c3-89e5c115cbf4';
    }

    protected function redirectTransaction(KickassTransaction $transaction)
    {
        /* Example
         * https://kickass.jash.fr/en/transactions/492131f6-7556-4735-a5c3-89e5c115cbf4/start
         */
        Tools::redirect('https://kickass.jash.fr/en/transactions/'.$transaction->transaction_id.'/start');
    }

    public function postProcess()
    {
        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice
            == 0 || !$this->module->active)
                Tools::redirect('index.php?controller=order&step=1');

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'kickass') {
                $authorized = true;
                break;
            }

        if (!$authorized)
                die($this->module->l('This payment method is not available.',
                    'validation'));

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer))
                Tools::redirect('index.php?controller=order&step=1');

        $currency = $this->context->currency;
        $total    = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $mailVars = array();
        $deal = $this->getCurrentDealDefinition();
        
        $this->module->validateOrder((int) $cart->id,
            Configuration::get('PS_OS_KICKASS'), $total,
            $this->module->displayName, NULL, $mailVars, (int) $currency->id,
            false, $customer->secure_key);
        
        $Transaction = $this->createTransaction($deal);
        if(Validate::isLoadedObject($Transaction)){
            $this->redirectTransaction($Transaction);
        }
        else{
            d($deal);
        }
    }
}