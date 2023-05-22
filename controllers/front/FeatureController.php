<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class FeatureController extends RestController {
    public function processGet() {
        if (!$this->key->can("product_features", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource && !array_key_exists("values", $_GET)) {
            $response = new Feature($id_resource);
        } else if (array_key_exists("values", $_GET) && $_GET["values"]) {
            $feature_values = FeatureValue::getFeatureValuesWithLang((int) $this->context->language->id, $id_resource);

            $response = $feature_values;
        } else {
            $features = Feature::getFeatures(
                (int) $this->context->language->id
            );

            $response = $features;
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }
}