<?php

class Product extends ProductCore {

    /**
     * Get all available products.
     * 
     * Overrided to enable searching functionality.
     *
     * @param int $id_lang Language identifier
     * @param int $start Start number
     * @param int $limit Number of products to return
     * @param string $order_by Field for ordering
     * @param string $order_way Way for ordering (ASC or DESC)
     * @param int|false $id_category Category identifier
     * @param bool $only_active
     * @param Context|null $context
     * @param string $like Product to find (by name or reference).
     *
     * @return array Products details
     */
    public static function getProducts(
        $id_lang,
        $start,
        $limit,
        $order_by,
        $order_way,
        $id_category = false,
        $only_active = false,
        Context $context = null,
        $like = ""
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)' .
                ($id_category ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` c ON (c.`id_product` = p.`id_product`)' : '') . '
                WHERE pl.`id_lang` = ' . (int) $id_lang .
                    ' AND p.state = 1' .
                    ($id_category ? ' AND c.`id_category` = ' . (int) $id_category : '') .
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                    ($only_active ? ' AND product_shop.`active` = 1' : '') . 
                    ($like != "" ? ' AND pl.`name` LIKE ' . pSQL($like) : '') . '
                ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) .
                ($limit > 0 ? ' LIMIT ' . (int) $start . ',' . (int) $limit : '');

        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }

        return $rq;
    }

}