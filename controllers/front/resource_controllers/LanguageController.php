<?php

use Ps_borest\Classes\Controller\RestController;

class LanguageController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = Language::getLanguage($id_resource);
        } else {
            $response = Language::getLanguages();
        }

        $response_json = json_encode($response);

        $this->ajaxRender($response_json);
    }

}