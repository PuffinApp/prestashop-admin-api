<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Lollilop\Classes\Controller\RestController;

// TODO: rename it in something else?
// TODO: create a hook to make it for good?
class LollilopDashboardModuleFrontController extends RestController
{
    public function processGet() {
        $module_name = Tools::getValue("module") ?? null;

        // If specific name is given, it gets widget only from that module.
        $params = [
            'module_name' => $module_name
        ];

        $modules_widgets = Hook::exec(
            'widgetCards', 
            $params, 
            null,
            true,
            true,
            false,
            $id_shop
        );

        die(
            json_encode($modules_widgets)
        );
    } 
}