<?php

require dirname(__FILE__) . "/../../vendor/autoload.php";

use Lollilop\Classes\Controller\RestController;

class LollilopNotificationsModuleFrontController extends RestController
{
    public function run() {
        global $kernel;

        if(!$kernel){ 
            require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
            $kernel = new \AppKernel('prod', false);
            $kernel->boot(); 
        }
        
        parent::run();
    }

    public function processGet() {
        $notification_obj = new \Notification();

        $notifications = $notification_obj->getAll();

        $notification_json = json_encode($notifications);

        $this->ajaxRender($notification_json);
    }
}