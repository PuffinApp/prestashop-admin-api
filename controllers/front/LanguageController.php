<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class LanguageController extends RestController {

    public function processGet() {
        if (!$this->key->can("languages", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = Language::getLanguage($id_resource);
        } else {
            $response = Language::getLanguages();
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }

}