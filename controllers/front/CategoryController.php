<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;

class CategoryController extends RestController {

    private $categoryProvider;

    public function __construct() {
        parent::__construct();

        $this->categoryProvider = $this->get("prestashop.core.api.category.repository");
    }
    public function processGet() {
        if (!$this->key->can("categories", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new Category($id_resource);
        } else {
            if (!isset ($_GET['page']) ) {  
                $page = 1;  
            } else {  
                $page = $_GET['page'];  
            }
            
            $results_per_page = 10;  

            $start = ($page-1) * $results_per_page;  
            $limit = $results_per_page;
            $order_by = '';

            if (array_key_exists("nested_categories", $_GET) && $_GET["nested_categories"]) {
                $categories =  $this->categoryProvider->getCategories(true);
                
                $categories = $categories["tree"]["children"];
            } else {
                $categories = Category::getCategories(
                    false,
                    false,
                    false,
                    '',
                    $order_by,
                    "LIMIT $limit OFFSET $start"
                );
            }
            $response = $categories;
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }
}