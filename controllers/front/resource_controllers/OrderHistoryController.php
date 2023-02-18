<?php

use Ps_borest\Classes\Controller\RestController;

class OrderHistoryController extends RestController {
    public function processPost() {
        $jsonPostData = $this->getJsonBody();

        if (!array_key_exists("id_order", $jsonPostData) || 
            !array_key_exists("id_order_state", $jsonPostData))
            $this->return400(); // This should be bad request?

        $id_order = $jsonPostData["id_order"];
        $id_order_state = $jsonPostData["id_order_state"];

        $order = new Order($id_order);

        if ($order->id == null)
            $this->return404();

        // Check for id_order_state too

        $order_history = new OrderHistory();
        $order_history->id_order = $id_order;
        $order_history->id_order_state = $id_order_state;
        $order_history->add();
    }
}