<?php

use Ps_borest\Classes\Controller\RestController;

class OrderStateController extends RestController {
    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new OrderState($id_resource);
        } else {
            $response = OrderState::getOrderStates($this->id_lang);
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }
}