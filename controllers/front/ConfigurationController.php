<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class ConfigurationController extends RestController
{
    public function processGet() {
        if (!$this->key->can("configurations", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $this->success(
            "OK",
            200,
            ""
        );
    } 
}