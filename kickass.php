<?php

class Kickass extends Module {

    private $loader = null;
    private $core;

    public function __construct()
    {
        $this->name = 'kickass';
        $this->author = 'Tomasz Wesołowski <twesolowski@jash.pl>';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->controllers = array('auth', 'payment', 'validation');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Kickass');
        $this->description = $this->l('Prestashop - kickass module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->core = require_once 'bootstrap/prestashop.php';

//        $c_tmp = $this->getConfigFieldsValues();
        $i = 1;
        if ((!isset($c_tmp['PS_KICKASS_CLIENT_ID']) || !isset($c_tmp['PS_KICKASS_REDIRECT_URL']) || empty($c_tmp['PS_KICKASS_CLIENT_ID']) || empty($c_tmp['PS_KICKASS_REDIRECT_URL'])))
                $this->warning .= '<br>' . $i++ . '. ' . $this->l('The client ID and Redirect url fields must be configured before using this module.');
        if ((!isset($c_tmp['PS_KICKASS_CLIENT_SECRET']) || empty($c_tmp['PS_KICKASS_CLIENT_SECRET'])))
                $this->warning .= '<br>' . $i++ . '. ' . $this->l('You need to authorize your shop with API before using this module.');
        if (!count(Currency::checkPaymentCurrencies($this->id)))
                $this->warning .= '<br>' . $i++ . '. ' . $this->l('No currency has been set for this module.');
    }

    public function install()
    {
        if (!parent::install() ||
                !Configuration::updateValue('PS_KICKASS_SCOPES', 'id;name') ||
                !$this->installTab() ||
                !$this->registerHook('payment') || ! $this->registerHook('displayPaymentEU') //|| !$this->registerHook('paymentReturn')
        ) 
        {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
                !Configuration::deleteByName('PS_KICKASS_CLIENT_ID') ||
                !Configuration::deleteByName('PS_KICKASS_CLIENT_SECRET') ||
                !Configuration::deleteByName('PS_KICKASS_REDIRECT_URL') ||
                !Configuration::deleteByName('PS_KICKASS_SCOPES') ||
                !$this->uninstallTab()
        )
        {
            return false;
        }
        return true;
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminKickass';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = 'Deal settings';
        $tab->id_parent = 0;
        $tab->module = $this->name;
        return $tab->add();
    }

    public function uninstallTab()
    {
            $id_tab = (int)Tab::getIdFromClassName('AdminKickass');
            if ($id_tab)
            {
            $tab = new Tab($id_tab);
            return $tab->delete();
            }
            else
            return false;
    }

    public function getContent2()
    {
            if (Tools::isSubmit('submitModule'))
            {

            /* Update Client ID */
            $clientId = Tools::getValue('PS_KICKASS_CLIENT_ID');
                if (isset($clientId))
                {
                $this->core->config()->update("PS_KICKASS_CLIENT_ID", $clientId);
            }

            /* Update Client Secret */
            $clientSecret = trim(Tools::getValue('PS_KICKASS_CLIENT_SECRET'));
                if (!empty($clientSecret))
                {
                $this->core->config()->setEncrypted("PS_KICKASS_CLIENT_SECRET", $clientSecret);
            }

            /* Update redirect URL */
            $redirectUrl = Tools::getValue('PS_KICKASS_REDIRECT_URL');
                if (isset($redirectUrl))
                {
                $this->core->config()->update("PS_KICKASS_REDIRECT_URL", $redirectUrl);
            }
            $updated_info = 1;
        }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKickass').'&updated_info='.(isset($updated_info)?$updated_info:0));
    }

    public function getContent()
    {
        $html = '';

        if (Tools::isSubmit('submitModule')) {
//            $this->getContent2();
            /* Update Client ID */
            $clientId = Tools::getValue('PS_KICKASS_CLIENT_ID');
            if (isset($clientId))
            {
                $this->core->config()->update("PS_KICKASS_CLIENT_ID", $clientId);
            }

            /* Update Client Secret */
            $clientSecret = trim(Tools::getValue('PS_KICKASS_CLIENT_SECRET'));
            if (!empty($clientSecret))
            {
                $this->core->config()->setEncrypted("PS_KICKASS_CLIENT_SECRET", $clientSecret);
            }

            /* Update redirect URL */
            $redirectUrl = Tools::getValue('PS_KICKASS_REDIRECT_URL');
            if (isset($redirectUrl))
            {
                $this->core->config()->update("PS_KICKASS_REDIRECT_URL", $redirectUrl);
            }

            $html .= $this->displayConfirmation($this->l('Info updated!'));
        }
        $html .= $this->renderForm();

        return $html;
    }

    public function getProvider()
    {
        return $this->core->getOAuthProvider();
    }

    public function renderForm()
    {
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Application Id'),
                        'name' => 'PS_KICKASS_CLIENT_ID'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Secret'),
                        'name' => 'PS_KICKASS_CLIENT_SECRET'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Callback url'),
                        'name' => 'PS_KICKASS_REDIRECT_URL'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        $html = '';
        $html .= $helper->generateForm(array($fieldsForm));

        $provider = $this->getProvider();

        $href = $provider->getAuthorizationUrl();
        $this->core->config()->setEncrypted("PS_KICKASS_OAUTH_STATE", $provider->state);
        $html .= '<a target="_blank" href="' . $href . '" >Connect</a>';
        $html .= $this->core->config()->getEncrypted("PS_KICKASS_API_TOKEN");;
        return $html;
    }
    
    
    
    public function hookPayment($params)
    {
            if (!$this->active)
                    return;
            if (!$this->checkCurrency($params['cart']))
                    return;

            $this->smarty->assign(array(
                    'this_path' => $this->_path,
                    'this_path_kickass' => $this->_path,
                    'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
            ));
            return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookDisplayPaymentEU($params)
    {
            if (!$this->active)
                    return;
            if (!$this->checkCurrency($params['cart']))
                    return;

            return array(
                    'cta_text' => $this->l('Pay by Kickass'),
                    'logo' => null,
                    'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
            );
    }

//    public function hookPaymentReturn($params)
//    {
//            if (!$this->active)
//                    return;
//
//            $state = $params['objOrder']->getCurrentState();
//            if (in_array($state, array(Configuration::get('PS_OS_CHEQUE'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'))))
//            {
//                    $this->smarty->assign(array(
//                            'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
//                            'chequeName' => $this->chequeName,
//                            'chequeAddress' => Tools::nl2br($this->address),
//                            'status' => 'ok',
//                            'id_order' => $params['objOrder']->id
//                    ));
//                    if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
//                            $this->smarty->assign('reference', $params['objOrder']->reference);
//            }
//            else
//                    $this->smarty->assign('status', 'failed');
//            return $this->display(__FILE__, 'payment_return.tpl');
//    }
    
    public function checkCurrency($cart)
    {
            $currency_order = new Currency((int)($cart->id_currency));
            $currencies_module = $this->getCurrency((int)$cart->id_currency);

            if (is_array($currencies_module))
                    foreach ($currencies_module as $currency_module)
                            if ($currency_order->id == $currency_module['id_currency'])
                                    return true;
            return false;
    }
    

    public function getConfigFieldsValues()
    {
        return array(
            'PS_KICKASS_CLIENT_ID' => $this->core->config()->get("PS_KICKASS_CLIENT_ID"),
            'PS_KICKASS_CLIENT_SECRET' => $this->core->config()->getEncrypted("PS_KICKASS_CLIENT_SECRET"),
            'PS_KICKASS_REDIRECT_URL' => $this->core->config()->get("PS_KICKASS_REDIRECT_URL")
        );
    }

}
