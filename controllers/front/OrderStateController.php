<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class OrderStateController extends RestController {
    public function processGet() {
        if (!$this->key->can("order_states", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

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