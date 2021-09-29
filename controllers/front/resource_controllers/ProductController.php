<?php

use Lollilop\Classes\Controller\RestController;

class ProductController extends RestController {

    public function processGet() {
        $id_resource = $_GET["id_resource"] ?? null;

        $response = null;

        if ($id_resource) {
            $product = new Product(
                                $id_resource,
                                true);

            if ($product->id == null) 
                $this->return404();
            
            $images = $product->getImages(1);

            $images_url = [];

            foreach($images as $image) {
                $images_url[] = $this->context->link->getImageLink($product->link_rewrite[1], $image["id_image"], ImageType::getFormatedName('home'));
            }

            $product->images_url = $images_url;

            $response = $product;
        } else {
            $id_lang=(int)Context::getContext()->language->id;
            $start=0;
            $limit=100;
            $order_by='id_product';
            $order_way='DESC';
            $id_category = false; 
            $only_active =true;
            $context = null;

            $products = Product::getProducts(
                                    $id_lang, $start, 
                                    $limit, $order_by, 
                                    $order_way, $id_category,
                                    $only_active , $context);

            // Get cover

            foreach ($products as &$product) {
                $images =  Product::getCover($product["id_product"]);

                $product["cover_url"] = $this->context->link->getImageLink($product["link_rewrite"], $images["id_image"], ImageType::getFormatedName('home'));
            }

            $response = $products;
        }

        $response_json = json_encode($response);

        $this->ajaxDie($response_json);
    }

}