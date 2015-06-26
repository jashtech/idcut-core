<?php

class IDcutPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl                 = true;
    public $display_column_left = false;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $error_messages = array();
        $cart           = $this->context->cart;
        if (!$this->module->checkCurrency($cart))
                Tools::redirect('index.php?controller=order');
        if (Tools::isSubmit('confirm_order')) {
            if (Tools::getValue('deal_join') == 0) {
                $deal           = null;
                $dealDefinition = null;
            } elseif ($deal_token = Tools::getValue('deal_token', false)) {
                $this->context->cookie->__unset('deal_hash');
                try {
                    $dealResponse = $this->module->core->getApiClient()->get('/deals/by_hash/'.$deal_token.'?expand=deal_definition');
                } catch (\Exception $e) {
                    $error_messages[] = Tools::displayError('Error when trying to get Deal Definition');
                }
                if ($dealResponse) {
                    $dealJson = $dealResponse->json();

                    if (isset($dealJson['deal_definition']['id'])) {
                        $deal                                  = $dealJson;
                        $dealJson['deal_definition']['ranges'] = isset($dealJson['deal_definition']['ranges'])
                                ? $dealJson['deal_definition']['ranges'] : array();
                        $dealDefinition                        = IDcut\Jash\Object\DealDefinition\DealDefinition::build($dealJson['deal_definition']);
                    } else {
                        $error_messages[] = Tools::displayError('There is no Deal Definition id');
                    }
                } else {
                    $error_messages[] = Tools::displayError('Empty Deal Definition response');
                }
            } else {
                $error_messages[] = Tools::displayError('You must specify deal token');
            }
            if (empty($error_messages)) {
                if ($this->module->checkDealConditions($cart, $dealDefinition,
                        $deal)) {
                    Tools::redirect($this->context->link->getModuleLink('idcut',
                            'validation', array(), true));
                } else {
                    $error_messages[] = Tools::displayError('Your cart is not fulfilling Deal conditions');
                }
            }
        }

        $this->context->smarty->assign(array(
            'error_messages' => $error_messages,
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int) $cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'deal_token' => Tools::getValue('deal_token',
                $this->context->cookie->deal_hash),
            'isoCode' => $this->context->language->iso_code,
            'this_path' => $this->module->getPathUri(),
            'this_path_idcut' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}