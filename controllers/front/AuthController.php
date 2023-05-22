<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class AuthController extends RestController
{
    public function processGet() {
        $this->success(
            "OK",
            200,
            ""
        );
    } 
}