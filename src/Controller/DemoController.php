<?php
// modules/your-module/src/Controller/DemoController.php

namespace MyModule\Controller;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class DemoController extends FrameworkBundleAdminController
{
    private $cache;
       
    // you can use symfony DI to inject services
    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }
    
    public function demoAction()
    {
        return $this->render('@Modules/your-module/templates/admin/configure.tpl');
    }
}