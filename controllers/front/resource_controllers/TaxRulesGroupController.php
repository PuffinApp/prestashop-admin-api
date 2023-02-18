<?php

use Ps_borest\Classes\Controller\RestController;

class TaxRulesGroupController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new TaxRulesGroup($id_resource);
        } else {
            $response = TaxRulesGroup::getTaxRulesGroupsForOptions();
        }

        $this->success(
            "OK",
            200,
            $response
        );
    }

}