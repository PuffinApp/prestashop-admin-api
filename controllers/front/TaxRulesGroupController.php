<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class TaxRulesGroupController extends RestController {

    public function processGet() {
        if (!$this->key->can("tax_rule_groups", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }
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