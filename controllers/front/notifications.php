<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Lollilop\Classes\Controller\RestController;

class LollilopNotificationsModuleFrontController extends RestController
{
    public function processGet() {
        $notification_obj = new \Notification();

        $notifications = $notification_obj->getAll();

        $notification_json = json_encode($notifications);

        $this->ajaxRender($notification_json);
    }
}