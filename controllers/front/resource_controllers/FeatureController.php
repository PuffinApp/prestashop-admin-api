<?php

use Ps_borest\Classes\Controller\RestController;

class FeatureController extends RestController {
    public function processGet() {
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

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }
}