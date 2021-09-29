<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Lollilop\Classes\Controller\RestController;

class LollilopResourcesModuleFrontController extends RestController
{
    private $resource_controller = null;

    public function run() {
        $resource_name = $_GET["resource"];
        $resource_name_capitalized = ucfirst(strtolower($resource_name));

        $resource_controller_name =  $resource_name_capitalized . "Controller";

        $resource_controller_file = dirname(__FILE__) . "/resource_controllers/" .  $resource_name_capitalized . "Controller.php";

        if (file_exists($resource_controller_file)) {
            require_once $resource_controller_file;

            $this->resource_controller = new $resource_controller_name();
        }

        if (is_null($this->resource_controller)) {
            // Resource does not exist, should it return a 404?
            return;
        }
        
        parent::run();
    }

    public function processGet() {
        $this->resource_controller->processGet();
    }

    public function processPost() {
        $this->resource_controller->processGet();
    }

    public function processPut() {
        $this->resource_controller->processGet();
    }

    public function processDelete() {
        $this->resource_controller->processGet();
    }
}