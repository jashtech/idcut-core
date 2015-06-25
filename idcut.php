<?php

if (!defined('_PS_VERSION_'))
    exit;
require_once(dirname(__FILE__) . '/classes/IDcutTransaction.php');
require_once(dirname(__FILE__) . '/classes/IDcutDealDefinition.php');
require_once(dirname(__FILE__) . '/classes/IDcutDeal.php');
require_once(dirname(__FILE__) . '/classes/IDcutRange.php');
class IDcut extends PaymentModule
{

    private $loader = null;
    public $core;

    public function __construct()
    {
        $this->name = 'idcut';
        $this->author = 'Tomasz Wesołowski <twesolowski@jash.pl>';
        $this->tab = 'payments_gateways';
        $this->version = '1.0';
        $this->controllers = array('auth', 'payment', 'validation');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('IDcut');
        $this->description = $this->l('Prestashop - idcut module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->core = require 'bootstrap/prestashop.php';

        $c_tmp = $this->getConfigFieldsValues();
        $i = 1;
        if ((!isset($c_tmp['PS_IDCUT_CLIENT_ID']) || !isset($c_tmp['PS_IDCUT_REDIRECT_URL']) || empty($c_tmp['PS_IDCUT_CLIENT_ID']) || empty($c_tmp['PS_IDCUT_REDIRECT_URL'])))
            $this->warning .= '<br>' . $i++ . '. ' . $this->l('The client ID and Redirect url fields must be configured before using this module.');
        if ((!isset($c_tmp['PS_IDCUT_CLIENT_SECRET']) || empty($c_tmp['PS_IDCUT_CLIENT_SECRET'])))
            $this->warning .= '<br>' . $i++ . '. ' . $this->l('You need to authorize your shop with API before using this module.');
        if (!count(Currency::checkPaymentCurrencies($this->id)))
            $this->warning .= '<br>' . $i++ . '. ' . $this->l('No currency has been set for this module.');
    }

    public function install()
    {
        if (!parent::install() ||
                !Configuration::updateValue('PS_IDCUT_SCOPES', 'id;name') ||
                !$this->installTabs() ||
                !$this->createOrderState() ||
                !$this->installDB() ||
                !$this->registerHook('payment') || !$this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (
                !parent::uninstall() ||
                !Configuration::deleteByName('PS_IDCUT_CLIENT_ID') ||
                !Configuration::deleteByName('PS_IDCUT_CLIENT_SECRET') ||
                !Configuration::deleteByName('PS_IDCUT_REDIRECT_URL') ||
                !Configuration::deleteByName('PS_IDCUT_SCOPES') ||
                !$this->uninstallTabs() ||
                !$this->uninstallDB()
        ) {
            return false;
        }
        return true;
    }

    protected function installDB(){
        return Db::getInstance()->Execute(
                'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'idcut_transaction` (
                    `id_idcut_transaction` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_order` INT( 10 ) UNSIGNED DEFAULT NULL,
                    `id_cart` INT( 10 ) UNSIGNED DEFAULT NULL,
                    `transaction_id` varchar(254) NOT NULL,
                    `deal_id` varchar(254) NOT NULL,
                    `status` TINYINT(4) UNSIGNED DEFAULT 0,
                    `title` varchar(254) DEFAULT NULL,
                    `amount` varchar(32) NOT NULL,
                    `amount_cents` BIGINT(20) UNSIGNED DEFAULT 0,
                    `amount_currency` varchar(3) NOT NULL,
                    `error_code` INT( 10 ) UNSIGNED DEFAULT NULL,
                    `message` text DEFAULT NULL,
                    `created_at` DATETIME DEFAULT NULL,
                    `date_edit` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id_idcut_transaction`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'idcut_deal_definition` (
                    `id_idcut_deal_definition` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `deal_definition_id` varchar(254) NOT NULL,
                    `active` TINYINT(1) UNSIGNED NOT NULL,
                    `ttl` INT( 10 ) UNSIGNED NOT NULL,
                    `locktime` INT( 10 ) UNSIGNED NOT NULL,
                    `user_max` INT( 10 ) UNSIGNED NOT NULL,
                    `min_order_value` INT( 10 ) UNSIGNED NOT NULL,
                    `range_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id_idcut_deal_definition`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'idcut_deal` (
                    `id_idcut_deal` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `deal_id` varchar(254) NOT NULL,
                    `created_at` DATETIME NOT NULL,
                    `updated_at` DATETIME NOT NULL,
                    `state` varchar(32) NOT NULL,
                    `ended` TINYINT(1) UNSIGNED NOT NULL,
                    `end_date` DATETIME NOT NULL,
                    `hash_id` varchar(254) NOT NULL,
                    `deal_definition_id` varchar(254) NOT NULL,
                    PRIMARY KEY (`id_idcut_deal`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'idcut_range` (
                    `id_idcut_range` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `deal_definition_id` varchar(254) NOT NULL,
                    `min_participants_number` INT( 10 ) UNSIGNED NOT NULL,
                    `discount_size` INT( 10 ) UNSIGNED NOT NULL,
                    PRIMARY KEY (`id_idcut_range`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;'
            );
    }

    public function installTabs()
    {
        $id_root_tab = $this->installTab('AdminIDcutDealDefinition', 'IDcut', 0);
        $ret = (int) $id_root_tab > 0 ? true : false;
        if ($ret) {
            $ret &= $this->installTab('AdminIDcutDealDefinition', 'Deal Definition', $id_root_tab) > 0 ? true : false;
            $ret &= $this->installTab('AdminIDcutDeal', 'Deals', $id_root_tab) > 0 ? true : false;
            $ret &= $this->installTab('AdminIDcutTransaction', 'Transactions', $id_root_tab) > 0 ? true : false;
            $ret &= $this->installTab('AdminIDcutStatus', 'Status', $id_root_tab) > 0 ? true : false;
        }

        return $ret;
    }

    public function installTab($class_name, $tab_name, $parent = 0)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;
        $tab->id_parent = $parent;
        $tab->module = $this->name;
        $tab->add();
        return (int) $tab->id;
    }

    protected function uninstallDB(){
        return Db::getInstance()->Execute(
                'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'idcut_transaction`;
                DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'idcut_deal_definition`;
                DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'idcut_deal`;
                DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'idcut_range`;'
            );
    }

    public function uninstallTabs()
    {
        $ret = true;
        $tabs = TabCore::getCollectionFromModule($this->name);
        foreach ($tabs->getAll()->getResults() as $tab) {
            $ret &= $tab->delete();
        }
        return $ret;
    }

    /**
     * Create a new order state
     */
    public function createOrderState()
    {
        if (!Configuration::get('PS_OS_IDCUT')) {
            $order_state = new OrderState();
            $order_state->name = array();

            foreach (Language::getLanguages() as $language) {
                $order_state->name[$language['id_lang']] = 'Waiting for payment IDcut';
            }

            $order_state->send_email = false;
            $order_state->color = '#4169E1';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = true;
            $order_state->invoice = false;

            if ($order_state->add()) {
                $source = dirname(__FILE__) . '/logo.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            } else {
                return false;
            }
            Configuration::updateValue('PS_OS_IDCUT', (int) $order_state->id);
        }
        if (!Configuration::get('PS_OS_IDCUT_PENDING')) {
            $order_state = new OrderState();
            $order_state->name = array();

            foreach (Language::getLanguages() as $language) {
                $order_state->name[$language['id_lang']] = 'Processing IDcut payment';
            }

            $order_state->send_email = false;
            $order_state->color = '#DDEEFF';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = true;
            $order_state->invoice = false;

            if ($order_state->add()) {
                $source = dirname(__FILE__) . '/logo.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            } else {
                return false;
            }
            Configuration::updateValue('PS_OS_IDCUT_PENDING', (int) $order_state->id);
        }
        return true;
    }

    public function getContent()
    {
        $html = '';

        if (Tools::isSubmit('submitModule')) {
//            $this->getContent2();
            /* Update Client ID */
            $clientId = Tools::getValue('PS_IDCUT_CLIENT_ID');
            if (isset($clientId)) {
                $this->core->config()->update("PS_IDCUT_CLIENT_ID", $clientId);
            }

            /* Update Client Secret */
            $clientSecret = trim(Tools::getValue('PS_IDCUT_CLIENT_SECRET'));
            if (!empty($clientSecret)) {
                $this->core->config()->setEncrypted("PS_IDCUT_CLIENT_SECRET", $clientSecret);
             
            }

            /* Update redirect URL */
            $redirectUrl = Tools::getValue('PS_IDCUT_REDIRECT_URL');
            if (isset($redirectUrl)) {
                $this->core->config()->update("PS_IDCUT_REDIRECT_URL", $redirectUrl);
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
                        'name' => 'PS_IDCUT_CLIENT_ID'
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->l('Secret'),
                        'name' => 'PS_IDCUT_CLIENT_SECRET'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Callback url'),
                        'name' => 'PS_IDCUT_REDIRECT_URL'
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

        $view = $this->core->getView();
        $view->setTemplateFile("connectButton.php");
        $view->connected = (bool)$this->core->config()->getEncrypted("PS_IDCUT_API_TOKEN");
        $view->authorizationUrl = $provider->getAuthorizationUrl();

        $this->core->config()->setEncrypted("PS_IDCUT_OAUTH_STATE", $provider->state);

        $html .= $view->render();
        
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
            'this_path_idcut' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
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
            'cta_text' => $this->l('Pay by IDcut'),
            'logo' => null,
            'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
        );
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active)
            return;

        $state = $params['objOrder']->getCurrentState();
        if (in_array($state, array(Configuration::get('PS_OS_IDCUT'), Configuration::get('PS_OS_IDCUT_PENDING'), Configuration::get('PS_OS_OUTOFSTOCK'),
                    Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'), Configuration::get('PS_OS_PAYMENT')))) {
            $this->smarty->assign(array(
                'total_to_pay' => Tools::displayPrice(
                        $params['total_to_pay'], $params['currencyObj'], false
                ),
                'status' => 'ok',
                'id_order' => $params['objOrder']->id
            ));
            if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
                $this->smarty->assign('reference', $params['objOrder']->reference);
        } else {
            $transaction = IDcutTransaction::getByOrderId($params['objOrder']->id);
            $this->smarty->assign(array(
                'status' => 'failed',
                'transaction' => $transaction
            ));
        }
        return $this->display(__FILE__, 'payment_return.tpl');
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency((int) ($cart->id_currency));
        $currencies_module = $this->getCurrency((int) $cart->id_currency);

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

    public function checkCartValue($cart, IDcut\Jash\Object\DealDefinition\DealDefinition $deal_definition = null)
    {
        $currency = $this->context->currency;
        
        $total = $cart->getOrderTotal(true, Cart::BOTH);
        if($currency->iso_code !== 'EUR' && $eur_id = CurrencyCore::getIdByIsoCode('EUR') && $currency_eur = CurrencyCore::getCurrency($eur_id)){
            $total = ToolsCore::convertPrice($total, $currency_eur, true, $this->context);
            $currency = $currency_eur;
        }

        $total_cents = (int)(($total*100));

        return (bool)($total_cents >= (int)$deal_definition->getMin_order_value());
    }
    
    public function checkDealConditions($cart, IDcut\Jash\Object\DealDefinition\DealDefinition &$deal_definition = null, &$deal = null)
    {
        if($deal === null){
            try {
                $dealCreateResponse = $this->core->getApiClient()->post('/deals');
            } catch (\Exception $e) {
                return false;
            }

            if((int)$dealCreateResponse->getStatusCode() !== 201 || !$dealCreateResponse->hasHeader('location')){
                return false;
            }

            try {
                $location = $dealCreateResponse->getHeader('location');
                $dealResponse = $this->core->getApiClient()->get($location.'?expand=deal_definition');
            } catch (\Exception $e) {
                return false;
            }

            if (!$dealResponse) {
                return false;
            }

            $dealJson = $dealResponse->json();
            if(!isset($dealJson['deal_definition']['id'])){
                return false;
            }

            $deal = $dealJson;
            $dealJson['deal_definition']['ranges'] = isset($dealJson['deal_definition']['ranges'])?$dealJson['deal_definition']['ranges']:array();
            $deal_definition = IDcut\Jash\Object\DealDefinition\DealDefinition::build($dealJson['deal_definition']);
        }elseif($deal['state'] == 'closed'){
            return false;
        }
        
        if(
            $deal['ended'] ||
            (int)$deal['counts']['paid_deal_participants_count'] >= (int)$deal_definition->getUser_max() ||
            !$this->checkCartValue($cart,$deal_definition)
            )
        {
            return false;
        }

        $IDcutTransaction = IDcutTransaction::getByCartId($cart->id);
        $IDcutTransaction->id_cart = $cart->id;
        $IDcutTransaction->deal_id = $deal['id'];

        $IDcutTransaction->setStatus('init');
        $IDcutTransaction->amount = Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH));

        $IDcutTransaction->setAmount_cents_AND_currency($cart->getOrderTotal(true, Cart::BOTH), $this->context->currency);

        $IDcutTransaction->save();

        return true;
    }

    public function getConfigFieldsValues()
    {
        return array(
            'PS_IDCUT_CLIENT_ID' => $this->core->config()->get("PS_IDCUT_CLIENT_ID"),
            'PS_IDCUT_CLIENT_SECRET' => $this->core->config()->getEncrypted("PS_IDCUT_CLIENT_SECRET"),
            'PS_IDCUT_REDIRECT_URL' => $this->core->config()->get("PS_IDCUT_REDIRECT_URL")
        );
    }
    public function getMyControllersUrls()
    {
        return array(
            'base' => $this->context->link->getPageLink('index'),
            'transaction' => $this->context->link->getPageLink('index').'?fc=module&module='.$this->name.'&controller=transaction', // Payment return
            'status_update' => $this->context->link->getPageLink('index').'?fc=module&module='.$this->name.'&controller=status_update', // update status of order
            'join_deal_url' => $this->context->link->getPageLink('index').'?fc=module&module='.$this->name.'&controller=deal_with_it', // save deal hash in session
            'ModuleConfiguration' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name, // IDcut module configuration link
        );
    }

}
