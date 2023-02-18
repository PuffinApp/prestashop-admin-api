<?php

require_once _PS_MODULE_DIR_ . "ps_borest/packages/DynamicWidgetForFlutter/vendor/autoload.php";

use agostinofiscale\DynamicWidgetForFlutter;
use agostinofiscale\DynamicWidgetForFlutter\FontWeight;
use agostinofiscale\DynamicWidgetForFlutter\ListTile;
use agostinofiscale\DynamicWidgetForFlutter\TextOverflow;
use agostinofiscale\DynamicWidgetForFlutter\TextStyle;

class dashproductsOverride extends dashproducts
{
    public function hookWidgetCards($params)
    {
        $params['date_from'] = 0;
        $params['date_to'] = 0;

        $date_from = $params['date_from'];
        $date_to = $params['date_to'];

        $orders_list_tiles = $this->getTilesRecentOrders();
        $best_sellers_tiles = $this->getTilesBestSellers($date_from, $date_to);
        $most_viewed_tiles = $this->getTilesMostViewed($date_from, $date_to);
        $top_10_most_search_tiles = $this->getTilesTop10MostSearch($date_from, $date_to);

        $ps_widget = new DynamicWidgetForFlutter\PsWidget(
            (new DynamicWidgetForFlutter\Text("Prodotti e vendite"))->setStyle(
                (new TextStyle())->setFontWeight(FontWeight::w700)
            ),
            null,
            [
                new DynamicWidgetForFlutter\Tab("Ordini recenti"),
                new DynamicWidgetForFlutter\Tab('Migliori vendite'),
                new DynamicWidgetForFlutter\Tab('Piu\' visualizzati'),
                new DynamicWidgetForFlutter\Tab('Piu\' ricercati')
            ],
            [
                (new DynamicWidgetForFlutter\ListView())->setChildren($orders_list_tiles)->setShrinkWrap(true),
                (new DynamicWidgetForFlutter\ListView())->setChildren($best_sellers_tiles)->setShrinkWrap(true),
                (new DynamicWidgetForFlutter\ListView())->setChildren($most_viewed_tiles)->setShrinkWrap(true),
                (new DynamicWidgetForFlutter\ListView())->setChildren($top_10_most_search_tiles)->setShrinkWrap(true),
            ],
            null
        );

        return json_encode($ps_widget);
    }

    private function getTilesRecentOrders()
    {
        $orders_list_tiles = [];

        $limit = (int)Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER') ? (int)Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER') : 10;
        $orders = Order::getOrdersWithInformations($limit);

        if (!count($orders)) {
            $order_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    "Nessun risultato"
                )
            );

            array_push($orders_list_tiles, $order_tile);

            return $orders_list_tiles;
        }

        foreach ($orders as $order) {
            $order_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    "#" . Tools::htmlentitiesUTF8($order["reference"])
                ),
                new DynamicWidgetForFlutter\Text(
                    Tools::htmlentitiesUTF8($order['firstname']) . ' ' . Tools::htmlentitiesUTF8($order['lastname'])
                ),
                new DynamicWidgetForFlutter\Text(
                    Tools::displayPrice((float)$order['total_paid_tax_excl'], Currency::getCurrency((int)$order['id_currency']))
                )
            );

            array_push($orders_list_tiles, $order_tile);
        }

        return $orders_list_tiles;
    }

    // TODO: Improve ListTile...
    private function getTilesBestSellers($date_from, $date_to)
    {
        $best_sellers_tiles = [];

        $products = Db::getInstance()->ExecuteS(
            '
                SELECT
                    product_id,
                    product_name,
                    SUM(product_quantity-product_quantity_refunded-product_quantity_return-product_quantity_reinjected) as total,
                    p.price as price,
                    pa.price as price_attribute,
                    SUM(total_price_tax_excl / conversion_rate) as sales,
                    SUM(product_quantity * purchase_supplier_price / conversion_rate) as expenses
                FROM `' . _DB_PREFIX_ . 'orders` o
    LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON o.id_order = od.id_order
    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = product_id
    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = od.product_attribute_id
    WHERE `invoice_date` BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
    AND valid = 1
    ' . Shop::addSqlRestriction(false, 'o') . '
    GROUP BY product_id, product_attribute_id
    ORDER BY total DESC
    LIMIT ' . (int)Configuration::get('DASHPRODUCT_NBR_SHOW_BEST_SELLER')
        );

        if (!count($products)) {
            $best_seller_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    "Nessun risultato"
                )
            );

            $best_seller_tile->setEnabled(false);

            array_push($best_sellers_tiles, $best_seller_tile);

            return $best_sellers_tiles;
        }

        foreach ($products as $best_seller) {
            $best_seller_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    Tools::htmlentitiesUTF8($best_seller['product_name'])
                )
            );

            array_push($best_sellers_tiles, $best_seller_tile);
        }

        return $best_sellers_tiles;
    }

    // TODO: Fix the text who gets cut
    private function getTilesMostViewed($date_from, $date_to)
    {
        $most_viewed_tiles = [];

        $products = null;

        if (Configuration::get('PS_STATSDATA_PAGESVIEWS')) {
            $products = $this->getTotalViewed($date_from, $date_to, (int)Configuration::get('DASHPRODUCT_NBR_SHOW_MOST_VIEWED'));
        } else {
            $most_viewed_tile = new ListTile(
                null,
                null,
                (new DynamicWidgetForFlutter\Text(
                    $this->trans('You must enable the "Save global page views" option from the "Data mining for statistics" module in order to display the most viewed products, or use the Google Analytics module.', array(), 'Modules.Dashproducts.Admin'),
                ))->setOverflow(TextOverflow::clip)
            );

            array_push($most_viewed_tiles, $most_viewed_tile);

            return $most_viewed_tiles;
        }

        if (is_null($products) || !count($products)) {
            $most_viewed_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    "Nessun risultato"
                )
            );

            array_push($most_viewed_tiles, $most_viewed_tile);

            return $most_viewed_tiles;
        }

        foreach ($products as $product) {
            $most_viewed_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    Tools::htmlentitiesUTF8($product['product_name'])
                )
            );

            array_push($most_viewed_tiles, $most_viewed_tile);
        }

        return $most_viewed_tiles;
    }

    private function getTilesTop10MostSearch($date_from, $date_to)
    {
        $top_10_most_search_tiles = [];

        $terms = $this->getMostSearchTerms($date_from, $date_to, (int)Configuration::get('DASHPRODUCT_NBR_SHOW_TOP_SEARCH'));

        if (!count($terms)) {
            $most_search_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    "Nessun risultato"
                )
            );

            array_push($top_10_most_search_tiles, $most_search_tile);

            return $top_10_most_search_tiles;
        }

        foreach ($terms as $term) {
            $most_search_tile = new DynamicWidgetForFlutter\ListTile(
                false,
                null,
                new DynamicWidgetForFlutter\Text(
                    Tools::htmlentitiesUTF8($term['product_name'])
                )
            );

            array_push($top_10_most_search_tiles, $most_search_tile);
        }

        return $top_10_most_search_tiles;
    }
}
