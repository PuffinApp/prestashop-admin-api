<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Ps_borest\Classes\Controller\RestController;

class Ps_borestNotificationsModuleFrontController extends RestController
{
    public function processGet() {
        $notification_obj = new \Notification();

        $notifications = $notification_obj->getAll();

        $this->success(
            "OK",
            200,
            $notifications
        );
    }
}