<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class ManufacturerController extends RestController {

    public function processGet() {
        if (!$this->key->can("manufacturers", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = Manufacturer::getNameById($id_resource);
        } else {
            
            $response =  Manufacturer::getManufacturers(false, (int) $this->context->language->id, true, false, false, false, true);
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }

}