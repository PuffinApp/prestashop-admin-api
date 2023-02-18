<?php

use Ps_borest\Classes\Controller\RestController;

class CarrierController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new Carrier($id_resource);
        } else {
            
            $response =  Carrier::getCarriers((int) $this->context->language->id);
        }

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }

}