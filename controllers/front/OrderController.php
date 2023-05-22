<?php

use Und3fined\Module\AdminApi\Classes\Controller\RestController;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;

class OrderController extends RestController
{

    public function processGet()
    {
        if (!$this->key->can("orders", "GET")) {
            $this->error(
                "",
                403,
                "You don't have permission to perform this request."
            );
        }

        $id_resource = $_GET["id_resource"] ?? null;
        $sub_resource = $_GET["sub_resource"] ?? null;

        $response = null;

        if (!is_null($sub_resource)) {
            switch ($sub_resource) {
                case "countries":
                    $orderCountriesProvider = $this->get("prestashop.adapter.form.choice_provider.order_countries");

                    $countries = $orderCountriesProvider->getChoices();

                    $response = $this->adaptCountries($countries);

                    break;
            }
        } else if ($id_resource) {
            $order = new Order($id_resource);

            if ($order->id == null)
                $this->error("", 404, "Resource not found");

            // Look: getProductsDetail() in Order.php as alternative
            $order->order_details = $order->getOrderDetailList();

            // Todo: is to check $images lenght something to do?
            foreach ($order->order_details as &$order_detail) {
                $product = new Product($order_detail["product_id"]);
                    
                
                $images = Product::getCover($order_detail["product_id"]);
                $order_detail["cover_url"] = $this->context->link->getImageLink($product->link_rewrite[1], $images["id_image"], $this->getFormattedName('home'));
            }           
            
            $order->history = $order->getHistory(Context::getContext()->language->id);
            $order->customer = $order->getCustomer();
            $order->address_delivery = new Address($order->id_address_delivery);
            $order->address_invoice = new Address($order->id_address_invoice);
            

            $response = $order;
        } else {
            //determine which page number visitor is currently on  
            if (!isset($_GET['page'])) {
                $page = 1;
            } else {
                $page = $_GET['page'];
            }

            //define total number of results you want per page  
            $results_per_page = 10;

            $start = ($page - 1) * $results_per_page;
            $limit = $results_per_page;
            $order_by = 'id_order';
            $order_way = 'DESC';

            $orderFilters = new OrderFilters(
                [
                    'limit' => $limit,
                    'offset' => $start,
                    'orderBy' => $order_by,
                    'sortOrder' => $order_way,
                    "filters" => $this->extractFilters($_GET)
                ]
            );


            $orderGrid = $this->get('prestashop.core.grid.factory.order')->getGrid($orderFilters);

            $response = $orderGrid->getData()->getRecords()->all();
        }

        $this->success(
            "OK",
            "200",
            $response
        );
    }

    private function extractFilters($filters)
    {
        $data = array_intersect_key(
            $filters,  /* main array*/
            array_flip( /* to be extracted */
                array(
                    'id_order', 'reference', 'new', 'country_name', 'customer', 'total_paid_tax_incl',
                    'payment', 'osname', 'date_from', 'date_to'
                )
            )
        );

        $clean_data = array_filter( $data, 'strlen' );

        foreach (["date_from", "date_to"] as $data) {
            if (array_key_exists($data, $clean_data)) {
                $clean_data["date_add"][explode("_", $data)[1]] = $clean_data[$data];
                unset($clean_data[$data]);
            }
        }

        return $clean_data;
    }

    private function adaptCountries($countries) {
        $countries_map = [];

        foreach($countries as $name => $id) {
            $countries_map[] = [
                "id" => $id,
                "name" => $name
            ];
        }

        return $countries_map;
    }
    
    private function getFormattedName($type) {
        $formatted_type = "";
        
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $formatted_type = ImageType::getFormattedName($type);
        } else {
            $formatted_type = ImageType::getFormatedName($type);
        }
        
        return $formatted_type;
    }
}
