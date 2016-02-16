<?php

class IDcutValidationModuleFrontController extends ModuleFrontController
{
    public $ssl                 = true;
    public $display_column_left = false;
    public $display_column_right = false;
    
    public function __construct()
    {
        $this->action = 'view';
        parent::__construct();
    }
    
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
        } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
            $this->errors[] = Tools::displayError('Error when trying to Create Transaction');
        }

        if (!$transactionCreateResponse instanceof GuzzleHttp\Psr7\Response || (int) $transactionCreateResponse->getStatusCode() !== 201 || !$transactionCreateResponse->hasHeader('location')) {
            return false;
        }

        try {
            $location            = $transactionCreateResponse->getHeader('location');
            $location            = $location[0];
            $transactionResponse = $this->module->core->getApiClient()->get($location);
        } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
            $this->errors[] = Tools::displayError('Error with retriving created transaction data');
            return false;
        }

        if (!$transactionResponse instanceof GuzzleHttp\Psr7\Response) {
            return false;
        }

        $transactionJson = Tools::jsonDecode($transactionResponse->getBody(), true);
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

        if (!$authorized){
                $this->errors = $this->l('This payment method is currently not available for that adress.',
                    'validation');
                return false;
        }

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer))
                Tools::redirect('index.php?controller=order&step=1');

        $currency = $this->context->currency;
        $total    = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $mailVars = array();

        $transaction = IDcutTransaction::getByCartId($cart->id);
        $transaction->setAmount_cents_AND_currency($total, $currency);

        if(!$this->module->validateOrder((int) $cart->id,
            Configuration::get('PS_OS_IDCUT'), $total,
            $this->module->displayName, NULL, $mailVars, (int) $currency->id,
            false, $customer->secure_key)){
            $this->errors[] = Tools::displayError('Order is not valid');
            return false;
        }

        $transaction->id_order = $this->module->currentOrder;
        $transaction->title    = $this->module->l('Order:').' '.Order::getUniqReferenceOf($this->module->currentOrder);

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
            $this->errors[] = Tools::displayError('Wrong API response');
            return false;
        }
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $this->context->smarty->assign(array(
            'payment_errors' => $this->errors,
        ));

        $this->setTemplate('validation.tpl');
    }
}