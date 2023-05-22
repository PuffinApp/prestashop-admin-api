<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class ContextController extends RestController
{

    public function processGet()
    {
        if (!$this->key->can("shops", "GET") || !$this->key->can("shop_groups", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }
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
        

        $this->success(
            "OK",
            "200",
            $response
        );
    }

    private function cleanKeys($temp_shops) {
        $shops = array_values($temp_shops);

        foreach ($shops as &$shop) {
            $shop["shops"] = array_values($shop["shops"]);
        }

        return $shops;
    }
}
