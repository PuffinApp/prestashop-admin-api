<?php

use Ps_borest\Classes\Controller\RestController;

class ManufacturerController extends RestController {

    public function processGet() {
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