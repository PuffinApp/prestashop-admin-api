<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Ps_borest\Classes\Controller\RestController;

class Ps_borestAuthModuleFrontController extends RestController
{
    public function processGet() {
        return true;
    } 
}