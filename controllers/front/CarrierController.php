<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class CarrierController extends RestController {

    public function processGet() {
        if (!$this->key->can("carriers", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new Carrier($id_resource);
        } else {
            
            $response =  Carrier::getCarriers((int) $this->context->language->id);
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }

}