<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class NotificationController extends RestController
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