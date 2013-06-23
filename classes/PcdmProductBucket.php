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
    const VOID_PRODUCT = -1;

    protected $do_not_translate;

    public function __construct() {

        $this->do_not_translate = array(
            'prod_a',
            'prod_b',
            'prod_c',
            'prod_d',
        );
        //definizione del tipo di dato
        add_action('init', array(&$this, 'defineType'));
        //definizione dei box aggiuntivi
        add_filter('cmb_meta_boxes', array(&$this, 'defineFields'));
        add_filter('pll_copy_post_metas', array(&$this, 'avoidTranslation'));
        //definizione dei nuovi parametri in griglia
        add_filter(sprintf("manage_%s_posts_columns", self::TYPE_IDENTIFIER), array(&$this, 'changeColumns'));
        add_action("manage_posts_custom_column", array(&$this, "fillColumns"), 10, 2);
        add_action('save_post', array(&$this, 'save'));
    }

    /**
     * Per evitare la sincronizzazione di alcuni campi
     * 
     * @param type $metas
     * @return type
     */
    public function avoidTranslation($metas) {
        foreach ($this->do_not_translate as $key) {
            $key = array_search(self::TYPE_PREFIX . $key, $metas);
            if ($key) {
                unset($metas[$key]);
            }
        }
        return $metas;
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
            'supports' => array('title')
        );

        register_post_type(self::TYPE_IDENTIFIER, $args);
    }

    public function defineFields($meta_boxes) {

        $product_selector = array();
        $product_selector[] = array('name' => 'Vuoto', 'value' => self::VOID_PRODUCT);
        $product_selector = array_merge($product_selector, PcdmProduct::getProductsForSelection());

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_2',
            'title' => 'Template',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => false,
            'fields' => array(
                array(
                    'name' => 'Template',
                    'desc' => 'Single product or up to 4 products',
                    'id' => self::TYPE_PREFIX . 'collection_template',
                    'type' => 'radio_inline',
                    'options' => array(
                        array('name' => 'Single Product', 'value' => self::TPL_SINGLE),
                        array('name' => 'Multiple Product', 'value' => self::TPL_MULTIPLE),
                    )
                ),
            )
        );



        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_3',
            'title' => 'Products',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => false,
            'fields' => array(
                array(
                    'name' => 'Product A',
                    'id' => self::TYPE_PREFIX . 'prod_a',
                    'type' => 'select',
                    'options' => $product_selector
                ),
                array(
                    'name' => 'Product B',
                    'id' => self::TYPE_PREFIX . 'prod_b',
                    'type' => 'select',
                    'options' => $product_selector
                ),
                array(
                    'name' => 'Product C',
                    'id' => self::TYPE_PREFIX . 'prod_c',
                    'type' => 'select',
                    'options' => $product_selector
                ),
                array(
                    'name' => 'Product D',
                    'id' => self::TYPE_PREFIX . 'prod_d',
                    'type' => 'select',
                    'options' => $product_selector
                ),
            )
        );


        return $meta_boxes;
    }

    public function changeColumns($cols) {

        $new_cols = array(
            self::TYPE_PREFIX . 'collection_template' => __('Template', 'trans'),
        );
        return array_merge($cols, $new_cols);
    }

    function fillColumns($column, $post_id) {
        switch ($column) {
            case self::TYPE_PREFIX . 'collection_template':
                $template = get_post_meta($post_id, self::TYPE_PREFIX . 'collection_template', true);
                echo ($template == self::TPL_MULTIPLE) ? 'Multiple Product' : 'Single Product';
                break;
        }
    }

    public function save($_id) {
        
    }

}