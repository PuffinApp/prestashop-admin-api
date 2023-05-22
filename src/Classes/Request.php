<?php

namespace Und3fined\Module\AdminApi\Classes;

use Configuration;
use Hook;

class Request {

    public static function isRestApiEnabled() {
        return Configuration::get('PS_ADMINAPI_ENABLE_REST_API', false);
    }
    
    public static function getResources()
    {
        $resources = [
            'addresses' => ['description' => 'The Customer, Brand and Customer addresses', 'class' => 'Address'],
            'attachments' => ['description' => 'The product Attachments', 'class' => 'Attachment', 'specific_management' => true],
            'carriers' => ['description' => 'The Carriers', 'class' => 'Carrier'],
            'carts' => ['description' => 'Customer\'s carts', 'class' => 'Cart'],
            'cart_rules' => ['description' => 'Cart rules management', 'class' => 'CartRule'],
            'categories' => ['description' => 'The product categories', 'class' => 'Category'],
            'combinations' => ['description' => 'The product combinations', 'class' => 'Combination'],
            'configurations' => ['description' => 'Shop configuration', 'class' => 'Configuration'],
            'contacts' => ['description' => 'Shop contacts', 'class' => 'Contact'],
            'countries' => ['description' => 'The countries', 'class' => 'Country'],
            'currencies' => ['description' => 'The currencies', 'class' => 'Currency'],
            'customers' => ['description' => 'The e-shop\'s customers', 'class' => 'Customer'],
            'customer_threads' => ['description' => 'Customer services threads', 'class' => 'CustomerThread'],
            'customer_messages' => ['description' => 'Customer services messages', 'class' => 'CustomerMessage'],
            'deliveries' => ['description' => 'Product delivery', 'class' => 'Delivery'],
            'groups' => ['description' => 'The customer\'s groups', 'class' => 'Group'],
            'guests' => ['description' => 'The guests', 'class' => 'Guest'],
            'images' => ['description' => 'The images', 'specific_management' => true],
            'image_types' => ['description' => 'The image types', 'class' => 'ImageType'],
            'languages' => ['description' => 'Shop languages', 'class' => 'Language'],
            'manufacturers' => ['description' => 'The product brands', 'class' => 'Manufacturer'],
            'messages' => ['description' => 'The Messages', 'class' => 'Message'],
            'order_carriers' => ['description' => 'The Order carriers', 'class' => 'OrderCarrier'],
            'order_cart_rules' => ['description' => 'The Order cart rules', 'class' => 'OrderCartRule'],
            'order_details' => ['description' => 'Details of an order', 'class' => 'OrderDetail'],
            'order_histories' => ['description' => 'The Order histories', 'class' => 'OrderHistory'],
            'order_invoices' => ['description' => 'The Order invoices', 'class' => 'OrderInvoice'],
            'orders' => ['description' => 'The Customers orders', 'class' => 'Order'],
            'order_payments' => ['description' => 'The Order payments', 'class' => 'OrderPayment'],
            'order_states' => ['description' => 'The Order statuses', 'class' => 'OrderState'],
            'order_slip' => ['description' => 'The Order slips', 'class' => 'OrderSlip'],
            'price_ranges' => ['description' => 'Price ranges', 'class' => 'RangePrice'],
            'product_features' => ['description' => 'The product features', 'class' => 'Feature'],
            'product_feature_values' => ['description' => 'The product feature values', 'class' => 'FeatureValue'],
            'product_options' => ['description' => 'The product options', 'class' => 'AttributeGroup'],
            'product_option_values' => ['description' => 'The product options value', 'class' => 'Attribute'],
            'products' => ['description' => 'The products', 'class' => 'Product'],
            'states' => ['description' => 'The available states of countries', 'class' => 'State'],
            'stores' => ['description' => 'The stores', 'class' => 'Store'],
            'suppliers' => ['description' => 'The product suppliers', 'class' => 'Supplier'],
            'tags' => ['description' => 'The Products tags', 'class' => 'Tag'],
            'translated_configurations' => ['description' => 'Shop configuration', 'class' => 'TranslatedConfiguration'],
            'weight_ranges' => ['description' => 'Weight ranges', 'class' => 'RangeWeight'],
            'zones' => ['description' => 'The Countries zones', 'class' => 'Zone'],
            'employees' => ['description' => 'The Employees', 'class' => 'Employee'],
            'search' => ['description' => 'Search', 'specific_management' => true, 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'content_management_system' => ['description' => 'Content management system', 'class' => 'CMS'],
            'shops' => ['description' => 'Shops from multi-shop feature', 'class' => 'Shop'],
            'shop_groups' => ['description' => 'Shop groups from multi-shop feature', 'class' => 'ShopGroup'],
            'taxes' => ['description' => 'The tax rate', 'class' => 'Tax'],
            'stock_movements' => ['description' => 'Stock movements', 'class' => 'StockMvtWS', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'stock_movement_reasons' => ['description' => 'Stock movement reason', 'class' => 'StockMvtReason'],
            'warehouses' => ['description' => 'Warehouses', 'class' => 'Warehouse', 'forbidden_method' => ['DELETE']],
            'stocks' => ['description' => 'Stocks', 'class' => 'Stock', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'stock_availables' => ['description' => 'Available quantities', 'class' => 'StockAvailable', 'forbidden_method' => ['POST', 'DELETE']],
            'warehouse_product_locations' => ['description' => 'Location of products in warehouses', 'class' => 'WarehouseProductLocation', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'supply_orders' => ['description' => 'Supply Orders', 'class' => 'SupplyOrder', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'supply_order_details' => ['description' => 'Supply Order Details', 'class' => 'SupplyOrderDetail', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'supply_order_states' => ['description' => 'Supply Order Statuses', 'class' => 'SupplyOrderState', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'supply_order_histories' => ['description' => 'Supply Order Histories', 'class' => 'SupplyOrderHistory', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'supply_order_receipt_histories' => ['description' => 'Supply Order Receipt Histories', 'class' => 'SupplyOrderReceiptHistory', 'forbidden_method' => ['PUT', 'POST', 'DELETE']],
            'product_suppliers' => ['description' => 'Product Suppliers', 'class' => 'ProductSupplier'],
            'tax_rules' => ['description' => 'Tax rules entity', 'class' => 'TaxRule'],
            'tax_rule_groups' => ['description' => 'Tax rule groups', 'class' => 'TaxRulesGroup'],
            'specific_prices' => ['description' => 'Specific price management', 'class' => 'SpecificPrice'],
            'specific_price_rules' => ['description' => 'Specific price management', 'class' => 'SpecificPriceRule'],
            'shop_urls' => ['description' => 'Shop URLs from multi-shop feature', 'class' => 'ShopUrl'],
            'product_customization_fields' => ['description' => 'Customization Field', 'class' => 'CustomizationField'],
            'customizations' => ['description' => 'Customization values', 'class' => 'Customization'],
        ];

        $extra_resources = Hook::exec(
            'addAdminApiResources', 
            ['resources' => $resources], 
            null, 
            true, 
            false
        );

        if (is_countable($extra_resources) && count($extra_resources)) {
            foreach ($extra_resources as $new_resources) {
                if (is_countable($new_resources) && count($new_resources)) {
                    $resources = array_merge($resources, $new_resources);
                }
            }
        }

        ksort($resources);

        return $resources;
    }

    public static function getMethods() {
        $methods = array('GET', 'PUT', 'POST', 'DELETE', 'HEAD');

        return $methods;
    }
}