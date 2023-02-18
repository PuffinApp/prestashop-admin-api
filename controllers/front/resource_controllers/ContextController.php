<?php

use Ps_borest\Classes\Controller\RestController;

class ContextController extends RestController
{

    public function processGet()
    {
        /* $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = Manufacturer::getNameById($id_resource);
        } else {
            $response = Manufacturer::getManufacturers();
        }

        $response_json = json_encode($response);
 */ 
        // Check if multishop is enabled or less
        if (Shop::isFeatureActive()) {
            $temp_shops = Shop::getTree();

            $response = $this->cleanKeys($temp_shops);
        } else {
            $response = [];
        }
        

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }

    private function cleanKeys($temp_shops) {
        $shops = array_values($temp_shops);

        foreach ($shops as &$shop) {
            $shop["shops"] = array_values($shop["shops"]);
        }

        return $shops;
    }
}
