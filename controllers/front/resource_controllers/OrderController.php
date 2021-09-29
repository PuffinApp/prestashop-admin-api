<?php

use Lollilop\Classes\Controller\RestController;

class OrderController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $order = new Order($id_resource);

            if ($order->id == null) 
                $this->return404();

            $response = $order;
        } else {
            $response = Order::getOrdersWithInformations();
        }

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }

}