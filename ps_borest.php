<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ps_borest extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ps_borest';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Agostino Fiscale';
        $this->need_instance = 0;
        

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ps_borest');
        $this->description = $this->l('Ps_borest let you manage your shop from your phone');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('LOLLILOP_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        // Register hooks for overrides
        $this->registerOverrideHooks();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('moduleRoutes');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LOLLILOP_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        // Unregister hooks for overrides
        $this->unregisterOverrideHooks();

        return parent::uninstall() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('moduleRoutes');
    }

    public function registerOverrideHooks() {
        foreach ($this->getOverridedModules() as $module) {
            $module->registerHook('widgetCards');
        }
    }

    public function unregisterOverrideHooks() {
        foreach ($this->getOverridedModules() as $module) {
            $module->unregisterHook('widgetCards');
        }
    }

    public function getOverridedModules() {
        $modules = [];

        foreach($this->getOverrides() as $override) {
            // Check if it is a module
            $module = Module::getInstanceByName($override);

            if ($module) {
                $modules[] = $module;
            }
        } 

        return $modules;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPs_borestModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPs_borestModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'LOLLILOP_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'LOLLILOP_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'LOLLILOP_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'LOLLILOP_LIVE_MODE' => Configuration::get('LOLLILOP_LIVE_MODE', true),
            'LOLLILOP_ACCOUNT_EMAIL' => Configuration::get('LOLLILOP_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'LOLLILOP_ACCOUNT_PASSWORD' => Configuration::get('LOLLILOP_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookModuleRoutes()
    {
        return [
            'module-ps_borest-apiauth' => [
                'rule' => 'ps_borestapi/auth',
                'keywords' => [],
                'controller' => 'auth',
                'params' => [
                    'fc' => 'module',
                    'module' => 'ps_borest'
                ] 
            ],
            'module-ps_borest-apidashboard' => [
                'rule' => 'ps_borestapi/dashboard{/:module}',
                'keywords' => [
                    'module' => array('regexp' => '[\w]+', 'param' => 'module'),
                ],
                'controller' => 'dashboard',
                'params' => [
                    'fc' => 'module',
                    'module' => 'ps_borest'
                ] 
            ],
            'module-ps_borest-apiresources' => [
                'rule' => 'ps_borestapi/resources{/:resource}{/:id_resource}',
                'keywords' => [
                    'resource' => array('regexp' => '[\w]+', 'param' => 'resource'),
                    'id_resource' => array('regexp' => '[\w]+', 'param' => 'id_resource')
                ],
                'controller' => 'resources',
                'params' => [
                    'fc' => 'module',
                    'module' => 'ps_borest'
                ] 
            ],
            'module-ps_borest-apinotifications' => [
                'rule' => 'ps_borestapi/notifications',
                'keywords' => [],
                'controller' => 'notifications',
                'params' => [
                    'fc' => 'module',
                    'module' => 'ps_borest'
                ] 
            ]
        ];
    }
}
