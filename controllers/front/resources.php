<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Ps_borest\Classes\Controller\RestController;

class Ps_borestResourcesModuleFrontController extends RestController
{
    private $resource_controller = null;

    public function run() {
        $resource_controllers = Dispatcher::getControllersInDirectory(dirname(__FILE__) . "/resource_controllers/");

        $resource_name = $_GET["resource"];
		$resource_name_lower = strtolower($resource_name);

        if (array_key_exists($resource_name_lower, $resource_controllers)) {
					$resource_controller_name = $resource_controllers[$resource_name_lower];
			        $resource_controller_file = dirname(__FILE__) . "/resource_controllers/" . $resource_controller_name . ".php";

            require_once $resource_controller_file;

            $this->resource_controller = new $resource_controller_name();
        } else {
            $this->return404();
        }
        
        parent::run();
    }

    public function processGet() {
        $this->resource_controller->processGet();
    }

    public function processPost() {
        $this->resource_controller->processPost();
    }

    public function processPut() {
        $this->resource_controller->processPut();
    }

    public function processDelete() {
        $this->resource_controller->processDelete();
    }
}