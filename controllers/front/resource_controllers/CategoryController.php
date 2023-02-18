<?php

use Ps_borest\Classes\Controller\RestController;

class CategoryController extends RestController {
    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $response = new Category($id_resource);
        } if ($_GET["nested_categories"]) {
            $categories =  $categories = Category::getNestedCategories(
                /* false,
                false,
                true,
                '',
                $order_by,
                "LIMIT $limit OFFSET $start" */
            );

            $response = $categories;
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
            
            $categories = Category::getCategories(
                false,
                false,
                true,
                '',
                $order_by,
                "LIMIT $limit OFFSET $start"
            );

            $response = $categories;
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }
}