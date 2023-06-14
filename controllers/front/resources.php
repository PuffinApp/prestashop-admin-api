<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

use Doctrine\Inflector\InflectorFactory;

class Ps_adminapiResourcesModuleFrontController extends RestController
{
    private $resource_controller = null;

    public function run() {
        $resource_controllers = [];
        
        $controllers = Dispatcher::getControllersInDirectory(dirname(__FILE__));

        foreach ($controllers as $controller => $filename) {
            if ($controller === "resources") {
                continue;
            }

            $path = dirname(__FILE__) . '/' . $filename . ".php";

            $resource_controllers[$controller] = $path;
        }

        Hook::exec(
            "actionAdminApiResourcesControllers",
            array(
                "resource_controllers" => &$resource_controllers
            )
        );

        $resource_name = $_GET["resource"];
		$resource_name_lower = strtolower($resource_name);

        // Singularize the resource name
        $inflector = InflectorFactory::create()->build();
        $resource_name_singular = $inflector->singularize($resource_name_lower);

        if (array_key_exists($resource_name_singular, $resource_controllers)) {
			$resource_controller_file = $resource_controllers[$resource_name_singular];
            $resource_controller_name = ucfirst($resource_name_singular) . "Controller";

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