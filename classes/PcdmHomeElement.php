<?php

class PcdmHomeElement {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const TYPE_IDENTIFIER = 'pcdm_hpelement';
    const TPL_LARGE = 'hp_large';
    const TPL_MEDIUM = 'hp_medium';
    const TPL_SMALL = 'hp_small';
    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const TYPE_PREFIX = 'pcdm_hp_';

    public function __construct() {
        add_action('init', array(&$this, 'defineType'));
        add_filter('cmb_meta_boxes', array(&$this, 'defineFields'));
        //definizione dei nuovi parametri in griglia
//        add_filter(sprintf("manage_%s_posts_columns", self::TYPE_IDENTIFIER), array(&$this, 'changeColumns'));
//        add_action("manage_posts_custom_column", array(&$this, "fillColumns"), 10, 2);
    }

    /**
     * Definisce il tipo di dato Prodotto da console di amministrazione         
     */
    public function defineType() {

        $labels = array(
            'name' => _x('HP Elements', 'post type general name'),
            'singular_name' => _x('HP Element', 'post type singular name'),
            'add_new' => _x('Add New', 'portfolio item'),
            'add_new_item' => __('Add New HP Item'),
            'edit_item' => __('Edit HP Item'),
            'new_item' => __('New HP Item'),
            'view_item' => __('View HP Item'),
            'search_items' => __('Search HP Elements'),
            'not_found' => __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false, //non presenta gerarchia
            'menu_position' => null,
            'supports' => array('title', 'thumbnail')
        );

        register_post_type(self::TYPE_IDENTIFIER, $args);
    }

    public function defineFields($meta_boxes) {

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_1',
            'title' => 'Template',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => false,
            'fields' => array(
                array(
                    'name' => 'Template',
                    'desc' => 'Choose element template',
                    'id' => self::TYPE_PREFIX . 'hp_template',
                    'type' => 'radio_inline',
                    'options' => array(
                        array('name' => 'Small', 'value' => self::TPL_LARGE),
                        array('name' => 'Medium', 'value' => self::TPL_MEDIUM),
                        array('name' => 'Large', 'value' => self::TPL_SMALL),
                    )
                ),
                array(
                    'name' => 'Void After',
                    'desc' => 'Check if you want a void space after this element',
                    'id' => self::TYPE_PREFIX . 'void_after',
                    'type' => 'checkbox'
                ),
            )
        );

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_2',
            'title' => 'Description',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Number',
                    'desc' => 'Define the number of this element',
                    'id' => self::TYPE_PREFIX  . 'hp_number',
                    'type' => 'text_small'
                ),
                array(
                    'name' => 'Description',
                    'desc' => 'Insert a description for this element',
                    'id' => self::TYPE_PREFIX . 'description',
                    'type' => 'textarea_small'
                ),
            ),
        );

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_3',
            'title' => 'Link',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Link',
                    'desc' => 'User defined link',
                    'id' => self::TYPE_PREFIX  . 'hp_link',
                    'type' => 'text_medium'
                ),
            ),
        );

        return $meta_boxes;
    }

//    public function changeColumns($cols) {
//
//        $new_cols = array(
//            self::TYPE_PREFIX . 'collection_color' => __('Hover Color', 'trans'),
//        );
//        return array_merge($cols, $new_cols);
//    }
//
//    function fillColumns($column, $post_id) {
//        switch ($column) {
//            case self::TYPE_PREFIX . 'collection_color':
//                $color = get_post_meta($post_id, self::TYPE_PREFIX . 'collection_color', true);
//                echo sprintf("<span style=\"color:%s;font-weight:bold;\">%s</span>", $color, $color);
//                break;
//        }
//    }
}