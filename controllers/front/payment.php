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

        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart))
                Tools::redirect('index.php?controller=order');
        if (Tools::isSubmit('confirm_order')){
                if($this->module->checkDealConditions($cart)){
                    Tools::redirect($this->context->link->getModuleLink('idcut', 'validation', array(), true));
                }
                else{
                    $this->context->smarty->assign(array(
                        'error_message' => ToolsCore::displayError('Your cart is not fulfilling Deal conditions')
                    ));
                }
        }

        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int) $cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'isoCode' => $this->context->language->iso_code,
            'this_path' => $this->module->getPathUri(),
            'this_path_idcut' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}