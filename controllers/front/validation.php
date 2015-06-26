<?php

class IDcutValidationModuleFrontController extends ModuleFrontController
{

    protected function createTransaction(IDcutTransaction $transaction)
    {
        $transaction_body = new \IDcut\Jash\Object\Transaction\Transaction();

        $transaction_body->setAmount_cents($transaction->amount_cents);
        $transaction_body->setAmount_currency($transaction->amount_currency);
        $transaction_body->setTitle($transaction->title);
        $transaction_body->setDeal_id($transaction->deal_id);


        try {
            $transactionCreateResponse = $this->module->core->getApiClient()->post('/transactions',
                $transaction_body->__toStringForCreate());
        } catch (\Exception $e) {
            $error_messages[] = Tools::displayError('Error when trying to Create Transaction');
        }

        if ((int) $transactionCreateResponse->getStatusCode() !== 201 || !$transactionCreateResponse->hasHeader('location')) {
            return false;
        }

        try {
            $location            = $transactionCreateResponse->getHeader('location');
            $transactionResponse = $this->module->core->getApiClient()->get($location);
        } catch (\Exception $e) {
            return false;
        }

        if (!$transactionResponse) {
            return false;
        }

        $transactionJson = $transactionResponse->json();
        if (!isset($transactionJson['id'])) {
            return false;
        }

        return IDcut\Jash\Object\Transaction\Transaction::build($transactionJson);
    }

    protected function redirectTransaction(IDcut\Jash\Object\Transaction\Transaction $transaction)
    {
        Tools::redirect($transaction->getConfirm_payment_link());
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
            if ($module['name'] == 'idcut') {
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

        $transaction = IDcutTransaction::getByCartId($cart->id);
        $transaction->setAmount_cents_AND_currency($total, $currency);

        $this->module->validateOrder((int) $cart->id,
            Configuration::get('PS_OS_IDCUT'), $total,
            $this->module->displayName, NULL, $mailVars, (int) $currency->id,
            false, $customer->secure_key);

        $transaction->id_order = $this->module->currentOrder;
        $transaction->title    = $this->module->l('Order:').' '.OrderCore::getUniqReferenceOf($this->module->currentOrder);

        $transactionApi = $this->createTransaction($transaction);

        if ($transactionApi !== false) {
            $transaction->transaction_id = $transactionApi->getId();

            $Date      = strtotime($transactionApi->getCreated_at());
            $converted = date("Y-m-d H:i:s", $Date);

            $transaction->created_at      = $converted;
            $transaction->date_edit       = $converted;
            $transaction->title           = $transactionApi->getTitle();
            $transaction->setStatus($transactionApi->getStatus());
            $transaction->amount          = $transactionApi->getAmount();
            $transaction->amount_cents    = $transactionApi->getAmount_cents();
            $transaction->amount_currency = $transactionApi->getAmount_currency();
            $transaction->save();

            $this->redirectTransaction($transactionApi);
        } else {
            d(Tools::displayError('Error when trying to Create Transaction'));
        }
    }
}