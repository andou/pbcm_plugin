<?php

class PcdmProduct {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const TYPE_IDENTIFIER = 'pcdm_products';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const TYPE_PREFIX = 'pcdm_pr_';

    public function __construct() {
        add_action('init', array(&$this, 'defineType'));
        add_action('before_delete_post', array(&$this, 'delete'));
        add_filter('cmb_meta_boxes', array(&$this, 'defineFields'));
        //definizione dei nuovi parametri in griglia
        add_filter(sprintf("manage_%s_posts_columns", self::TYPE_IDENTIFIER), array(&$this, 'changeColumns'));
        add_action("manage_posts_custom_column", array(&$this, "fillColumns"), 10, 2);
    }

    /**
     * Definisce il tipo di dato Prodotto da console di amministrazione         
     */
    public function defineType() {

        $labels = array(
            'name' => _x('Products', 'post type general name'),
            'singular_name' => _x('Product', 'post type singular name'),
            'add_new' => _x('Add New', 'portfolio item'),
            'add_new_item' => __('Add New Product Item'),
            'edit_item' => __('Edit Product Item'),
            'new_item' => __('New Product Item'),
            'view_item' => __('View Product Item'),
            'search_items' => __('Search Product'),
            'not_found' => __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'products'),
            'capability_type' => 'post',
            'hierarchical' => false, //non presenta gerarchia
            'menu_position' => null,
            'supports' => array('title', 'thumbnail')
        );

        register_post_type(self::TYPE_IDENTIFIER, $args);
    }

    public static function getProductsForSelection($orderBy = 'title', $orderIn = 'ASC') {

        $products = array();

        $args = array(
            'post_type' => self::TYPE_IDENTIFIER,
            'post_status' => 'publish',
            'orderby' => $orderBy,
            'order' => $orderIn,
        );

        foreach (get_posts($args) as $product) {
            $products[] = array(
                'name' => $product->post_title,
                'value' => $product->ID
            );
        }


        return $products;
    }

    public function defineFields($meta_boxes) {
        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_1',
            'title' => 'Description',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Description',
                    'desc' => 'Insert a description for this product',
                    'id' => self::TYPE_PREFIX . 'description',
                    'type' => 'textarea_small'
                ),
            ),
        );

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_2',
            'title' => 'Appearance',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Color',
                    'desc' => 'Pick a color for the hover',
                    'id' => self::TYPE_PREFIX . 'collection_color',
                    'type' => 'colorpicker'
                ),
            ),
        );

        return $meta_boxes;
    }

    public function changeColumns($cols) {

        $new_cols = array(
            self::TYPE_PREFIX . 'collection_color' => __('Hover Color', 'trans'),
        );
        return array_merge($cols, $new_cols);
    }

    function fillColumns($column, $post_id) {
        switch ($column) {
            case self::TYPE_PREFIX . 'collection_color':
                $color = get_post_meta($post_id, self::TYPE_PREFIX . 'collection_color', true);
                echo sprintf("<span style=\"color:%s;font-weight:bold;\">%s</span>", $color, $color);
                break;
        }
    }

    public function delete($postid) {
        global $post_type;
        if ($post_type != self::TYPE_IDENTIFIER)
            return;

        $args = array(
            'post_type' => PcdmProductBucket::TYPE_IDENTIFIER
        );
        foreach (get_post($args) as $postinfo) {
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_a', $postid);
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_b', $postid);
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_c', $postid);
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_d', $postid);
        }
    }

}