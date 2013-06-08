<?php

class PcdmProductBucket {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const TYPE_IDENTIFIER = 'pcdm_product_buckets';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const TYPE_PREFIX = 'pcdm_pb';

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
                    'desc' => 'Pick a color for the hover selection',
                    'id' => self::TYPE_PREFIX . 'collection_color',
                    'type' => 'colorpicker'
                ),
                array(
                    'name' => 'Template',
                    'desc' => 'Single product or up to 4 products',
                    'id' => self::TYPE_PREFIX . 'collection_template',
                    'type' => 'radio',
                    'options' => array(
                        array('name' => 'Single Product', 'value' => 'sngl_prod_tpl'),
                        array('name' => 'Multiple Product', 'value' => 'mult_prod_tpl'),
                    )
                ),
            ),
        );

        return $meta_boxes;
    }

    public function save($_id) {
        
    }

}