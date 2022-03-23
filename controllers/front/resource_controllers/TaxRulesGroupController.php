<?php

use Lollilop\Classes\Controller\RestController;

class TaxRulesGroupController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new TaxRulesGroup($id_resource);
        } else {
            $response = TaxRulesGroup::getTaxRulesGroupsForOptions();
        }

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }

}