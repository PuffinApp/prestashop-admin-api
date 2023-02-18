<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Ps_borest\Classes\Controller\RestController;

class Ps_borestSettingsModuleFrontController extends RestController
{
    public function processGet() {
        $this->success(
            "OK",
            200,
            ""
        );
    } 
}