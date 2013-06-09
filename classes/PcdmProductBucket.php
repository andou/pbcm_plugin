<?php

class PcdmProductBucket {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const TYPE_IDENTIFIER = 'pcdm_product_buckets';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const TYPE_PREFIX = 'pcdm_pb_';
    const TPL_SINGLE = 'sngl_prod_tpl';
    const TPL_MULTIPLE = 'mult_prod_tpl';

    public function __construct() {
        //definizione del tipo di dato
        add_action('init', array(&$this, 'defineType'));
        //definizione dei box aggiuntivi
        add_action('cmb_meta_boxes', array(&$this, 'defineFields'));
        //definizione dei nuovi parametri in griglia
        add_filter(sprintf("manage_%s_posts_columns", self::TYPE_IDENTIFIER), array(&$this, 'changeColumns'));
        add_action("manage_posts_custom_column", array(&$this, "fillColumns"), 10, 2);
        add_action('save_post', array(&$this, 'save'));
    }

    /**
     * Definisce il tipo di dato Prodotto da console di amministrazione         
     */
    public function defineType() {

        $labels = array(
            'name' => _x('Product Buckets', 'post type general name'),
            'singular_name' => _x('Product Bucket', 'post type singular name'),
            'add_new' => _x('Add New', 'product item'),
            'add_new_item' => __('Add New Product Bucket Item'),
            'edit_item' => __('Edit Product Bucket Item'),
            'new_item' => __('New Product Bucket Item'),
            'view_item' => __('View Product Bucket Item'),
            'search_items' => __('Search Product Bucket'),
            'not_found' => __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false, //non presenta gerarchia
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail')
        );

        register_post_type(self::TYPE_IDENTIFIER, $args);
    }

    public function defineFields() {
        $meta_boxes[] = array(
            'id' => 'fieldset_1',
            'title' => 'Appearance',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true, // Show field names on the left
            'fields' => array(
                array(
                    'name' => 'Color',
                    'desc' => 'Pick a color for the hover',
                    'id' => self::TYPE_PREFIX . 'collection_color',
                    'type' => 'colorpicker'
                ),
                array(
                    'name' => 'Template',
                    'desc' => 'Single product or up to 4 products',
                    'id' => self::TYPE_PREFIX . 'collection_template',
                    'type' => 'radio',
                    'options' => array(
                        array('name' => 'Single Product', 'value' => self::TPL_SINGLE),
                        array('name' => 'Multiple Product', 'value' => self::TPL_MULTIPLE),
                    )
                ),
            ),
        );

        return $meta_boxes;
    }

    public function changeColumns($cols) {

        $new_cols = array(
            self::TYPE_PREFIX . 'collection_color' => __('Hover Color', 'trans'),
            self::TYPE_PREFIX . 'collection_template' => __('Template', 'trans'),
        );
        return array_merge($cols, $new_cols);
    }

    function fillColumns($column, $post_id) {
        switch ($column) {
            case self::TYPE_PREFIX . 'collection_color':
                $color = get_post_meta($post_id, self::TYPE_PREFIX . 'collection_color', true);
                echo sprintf("<span style=\"color:%s;font-weight:bold;\">%s</span>", $color, $color);
                break;
            case self::TYPE_PREFIX . 'collection_template':
                $template = get_post_meta($post_id, self::TYPE_PREFIX . 'collection_template', true);
                echo ($template == self::TPL_MULTIPLE) ? 'Multiple Product' : 'Single Product';
                break;
        }
    }

    public function save($_id) {
        
    }

}