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
        $this->controllers = array('auth');

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Kickass');
        $this->description = $this->l('Prestashop - kickass module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->core = require_once 'bootstrap/prestashop.php';
    }

    public function install()
    {
        if (!parent::install() ||
                !Configuration::updateValue('PS_KICKASS_SCOPES', 'id;name')
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
                !Configuration::deleteByName('PS_KICKASS_SCOPES')
        )
        {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        $html = '';

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

    public function getConfigFieldsValues()
    {
        return array(
            'PS_KICKASS_CLIENT_ID' => $this->core->config()->get("PS_KICKASS_CLIENT_ID"),
            'PS_KICKASS_CLIENT_SECRET' => $this->core->config()->getEncrypted("PS_KICKASS_CLIENT_SECRET"),
            'PS_KICKASS_REDIRECT_URL' => $this->core->config()->get("PS_KICKASS_REDIRECT_URL")
        );
    }

}
