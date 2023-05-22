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

require dirname(__FILE__) . "/vendor/autoload.php";

use Und3fined\Module\AdminApi\Classes\Key;
use Und3fined\Module\AdminApi\Classes\Request;

class Ps_adminapi extends Module
{
    protected $config_form = false;

    private $notifications = [];

    private $_html = "";

    public function __construct()
    {
        $this->name = 'ps_adminapi';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Und3fined';
        $this->need_instance = 0;
        
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Prestashop Admin Api');
        $this->description = $this->l('Exposes Prestashop Admin functionalities through REST API.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->_html = "";
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        // Register hooks for overrides
        $this->registerOverrideHooks();

        return parent::install() &&
            $this->registerHook('moduleRoutes');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        // Unregister hooks for overrides
        $this->unregisterOverrideHooks();

        return parent::uninstall() &&
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
        if (((bool)Tools::isSubmit('submitPs_adminapiModule')) == true) {
            $this->postProcess();
        }

         /**
         * If a key have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPs_adminapiSaveKey')) == true) {
            $this->postProcessSaveKey();
        }
        

        if (Tools::isSubmit("addps_adminapi_key") 
            || Tools::isSubmit("updateps_adminapi_key")
            || Tools::isSubmit("viewps_adminapi_key")) {
            return $this->renderAuthenticationKeyView();
        } else if (Tools::isSubmit("deleteps_adminapi_key") ||
        Tools::isSubmit("submitBulkdeleteps_adminapi_key")) {
            $this->processDeleteKeys();
        }

        return $this->renderConfigurationView();
    }
    
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderConfigurationView()
    {
        $keys = Key::getAll();

        $helperForm = new HelperForm();

        $helperForm->show_toolbar = false;
        $helperForm->table = $this->table;
        $helperForm->module = $this;
        $helperForm->default_form_language = $this->context->language->id;
        $helperForm->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helperForm->identifier = $this->identifier;
        $helperForm->submit_action = 'submitPs_adminapiModule';
        $helperForm->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helperForm->token = Tools::getAdminTokenLite('AdminModules');

        $helperForm->tpl_vars = array(
            'fields_value' => $this->getConfigurationFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $helperList = new HelperList();
     
        $helperList->shopLinkType = '';
         
        $helperList->simple_header = false;
         
        // Actions to be displayed in the "Actions" column
        $helperList->actions = array(
            'edit', 
            'delete', 
            'view'
        );
         
        $helperList->show_toolbar = true;

        $helperList->title = $this->trans('Authorization keys', array(), 'Admin.Advparameters.Feature');
        $helperList->title_icon = 'icon-key';
        $helperList->listTotal = count($keys);
        $helperList->table = 'ps_adminapi_key';
        $helperList->identifier = 'id_key';   
        $helperList->force_show_bulk_actions = true;

        $helperList->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $helperList->toolbar_btn['new'] =  array(
			'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addps_adminapi_key&token='.Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add new')
		);

        $fields_list = array(
            'id_key' => array(
                'title' => $this->trans('Id', array(), 'Admin.Advparameters.Feature'),
                'class' => 'fixed-width-md',
                'search'=> false,
                'orderby' => false,
            ),
            'key' => array(
                'title' => $this->trans('Key', array(), 'Admin.Advparameters.Feature'),
                'class' => 'fixed-width-md',
                'search'=> false,
                'orderby' => false,
            ),
            'description' => array(
                'title' => $this->trans('Key description', array(), 'Admin.Advparameters.Feature'),
                'align' => 'left',
                'search'=> false,
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->trans('Enabled', array(), 'Admin.Global'),
                'align' => 'center',
                'active' => 'active',
                'type' => 'bool',
                'search'=> false,
                'orderby' => false,
                'class' => 'fixed-width-xs'
            )
        );

        $helperList->show_toolbar = true;
        $helperList->token = Tools::getAdminTokenLite('AdminModules');
        $helperList->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $this->context->smarty->assign(
            [
                "notifications" => $this->notifications
            ]
        );

        $this->_html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/configure.tpl'
        );
        
        $this->_html .= $helperForm->generateForm(
            array(
                $this->getConfigurationForm(),
            )
        );

        $this->_html .= $helperList->generateList(
            $keys, 
            $fields_list
        );

        return $this->_html;
    }

    private function renderAuthenticationKeyView() {
        $key = new Key(Tools::getValue("id_key", null));
        $permissions = Key::getPermissionForAccount($key->key);
        $methods = Request::getMethods();
        $resources = Request::getResources();            

        $this->context->smarty->assign(
            [
                "notifications" => $this->notifications,
                "key" => $key,
                "resources" => $resources,
                "methods" => $methods,
                "permissions" => $permissions
            ]
        );

        $this->_html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/key/view.tpl'
        );

        return $this->_html;
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigurationForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Configuration', array(), 'Admin.Advparameters.Feature'),
                    'icon' => 'icon-cog'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Enable Prestashop REST API', array(), 'Admin.Global'),
                        'desc' => $this->l('Prima di attivare le webservice, assicurati di:
                        1. Controlla che sul server sia disponibile la riscrittura delle URL.
                        2. Verifica che i cinque metodi GET, POST, PUT, DELETE e HEAD siano supportati dal tuo server.'),
                        'name' => 'PS_ADMINAPI_ENABLE_REST_API',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', array(), 'Admin.Global')
                            )
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),       
                    'class' => 'btn btn-default pull-right'   
                ),
            )
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigurationFormValues()
    {
        return array(
            'PS_ADMINAPI_ENABLE_REST_API' => Configuration::get('PS_ADMINAPI_ENABLE_REST_API', false),
        );
    }

    /**
     * Save configuration form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigurationFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Save key form data.
     */
    protected function postProcessSaveKey()
    {
        $authorization_key = Tools::getValue("authorization_key");

        $authorization_key_obj = new Key(array_key_exists("id", $authorization_key) ? $authorization_key["id"] : null);
        $authorization_key_obj->key = $authorization_key["key"];
        $authorization_key_obj->description = $authorization_key["description"];
        $authorization_key_obj->active = $authorization_key["active"];

        $save_status = $authorization_key_obj->save();
        
        if (!$save_status) {
            $this->notifications[] = $this->displayError(
                $this->l('There was a problem saving the key...')
            );

            return;
        }

        $save_permission_status = Key::setPermissionForAccount(
            $authorization_key_obj->id,
            $authorization_key["permissions"] ?? []
        );

        if (!$save_permission_status) {
            $this->notifications[] = $this->displayError(
                $this->l('There was a problem saving the permission for the key...')
            );

            return;
        }

        $this->notifications[] = $this->displayConfirmation(
            $this->l('Key saved successfully.')
        );
    }
    
    /**
     * Delete single or multiple keys
     */
    private function processDeleteKeys() {
        $isBulkDelete = Tools::isSubmit("submitBulkdeleteps_adminapi_key");

        $keys = [Tools::getValue("id_key")];

        if ($isBulkDelete) {
            $keys = Tools::getValue("ps_adminapi_keyBox");
        }

        try {
            foreach ($keys as $id_key) {
                $authorization_key_obj = new Key($id_key);

                $authorization_key_obj->delete();
            }

            $this->notifications[] = $this->displayConfirmation(
                $this->l('Key deleted successfully.')
            );
        } catch (PrestaShopException $e) {
            $this->notifications[] = $this->displayError(
                $this->l('There was a problem deleting the keys...')
            );
        }
    }
    public function hookModuleRoutes()
    {
        return [
            'module-ps_adminapi-apiresources' => [
                'rule' => 'adminapi{/:resource}{/:id_resource}',
                'keywords' => [
                    'resource' => array('regexp' => '[\w]+', 'param' => 'resource'),
                    'id_resource' => array('regexp' => '[\w]+', 'param' => 'id_resource')
                ],
                'controller' => 'resources',
                'params' => [
                    'fc' => 'module',
                    'module' => 'ps_adminapi'
                ] 
            ],
        ];
    }
}
