<?php

use Lollilop\Classes\Controller\RestController;

class ManufacturerController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = Manufacturer::getNameById($id_resource);
        } else {
            
            $response =  Manufacturer::getManufacturers(false, (int) $this->context->language->id, true, false, false, false, true);
        }

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }

}