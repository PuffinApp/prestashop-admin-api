<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

use Doctrine\Inflector\InflectorFactory;

class Ps_adminapiResourcesModuleFrontController extends RestController
{
    private $resource_controller = null;

    public function run() {
        $resource_controllers = Dispatcher::getControllersInDirectory(dirname(__FILE__));

        $resource_name = $_GET["resource"];
		$resource_name_lower = strtolower($resource_name);

        // Singularize the resource name
        $inflector = InflectorFactory::create()->build();
        $resource_name_singular = $inflector->singularize($resource_name_lower);

        if (array_key_exists($resource_name_singular, $resource_controllers)) {
					$resource_controller_name = $resource_controllers[$resource_name_singular];
			        $resource_controller_file = dirname(__FILE__) . '/' . $resource_controller_name . ".php";

            require_once $resource_controller_file;

            $this->resource_controller = new $resource_controller_name();
        } else {
            $this->error(
                "", 
                404, 
                "Resource not found."
            );
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