<?php

class IDcutPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl                 = true;
    public $display_column_left = false;
    public $display_column_right = false;
    
    public function setMedia()
    {
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/css/front/payment.css');
        return parent::setMedia();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $error_messages = array();
        $cart           = $this->context->cart;
        if (!$this->module->checkModuleConfiguration($cart))
                Tools::redirect('index.php?controller=order');
        if (Tools::isSubmit('confirm_order')) {
            if (Tools::getValue('deal_join') == 0) {
                $deal           = null;
                $dealDefinition = null;
            } elseif ($deal_token = Tools::getValue('deal_token', false)) {
                try {
                    $dealResponse = $this->module->core->getApiClient()->get('/deals/by_hash/'.$deal_token.'?expand=deal_definition');
                    if ($dealResponse instanceof GuzzleHttp\Psr7\Response) {
                        $dealJson = Tools::jsonDecode($dealResponse->getBody(), true);

                        if (isset($dealJson['deal_definition']['id'])) {
                            $deal                                  = \IDcut\Jash\Object\Deal\Deal::build($dealJson);
                            $dealJson['deal_definition']['ranges'] = isset($dealJson['deal_definition']['ranges'])
                                    ? $dealJson['deal_definition']['ranges'] : array();
                            $dealDefinition                        = IDcut\Jash\Object\DealDefinition\DealDefinition::build($dealJson['deal_definition']);

                            $IDcutDeal = IDcutDeal::getByDealId($deal->getId());
                            if(empty($IDcutDeal->deal_id)){
                                $IDcutDeal->deal_id = $deal->getId();
                                $IDcutDeal->deal_definition_id = $deal_definition->getId();
                            }
                            $Date      = strtotime($deal->getCreated_at());
                            $converted = date("Y-m-d H:i:s", $Date);
                            $IDcutDeal->created_at         = $converted;

                            $Date      = strtotime($deal->getUpdated_at());
                            $converted = date("Y-m-d H:i:s", $Date);
                            $IDcutDeal->updated_at         = $converted;

                            $IDcutDeal->state              = $deal->getState();
                            $IDcutDeal->ended              = $deal->getEnded();

                            $Date      = strtotime($deal->getEnd_date());
                            $converted = date("Y-m-d H:i:s", $Date);
                            $IDcutDeal->end_date           = $converted;

                            $IDcutDeal->hash_id            = $deal->getHash_id();

                            $IDcutDeal->save();

                        } else {
                            $error_messages[] = Tools::displayError('There is no Deal Definition id');
                        }
                    } else {
                        $error_messages[] = Tools::displayError('Empty Deal Definition response');
                    }
                } catch (\IDcut\Jash\Exception\Prestashop\Exception $e) {
                    $error_messages[] = Tools::displayError('Error when trying to get Deal Definition');
                }
                
            } else {
                $error_messages[] = Tools::displayError('You must specify deal token');
            }
            if (empty($error_messages)) {
                if ($this->module->checkDealConditions($cart, $dealDefinition,
                        $deal)) {
                    $this->context->cookie->__unset('deal_hash');
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