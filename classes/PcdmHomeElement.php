<?php

class PcdmHomeElement {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const TYPE_IDENTIFIER = 'pcdm_hpelement';
    const TPL_LARGE = 'hp_large';
    const TPL_MEDIUM = 'hp_medium';
    const TPL_SMALL = 'hp_small';
    const LINK_TYPE_CUSTOM = 'custom';
    const LINK_TYPE_PRODUCT = 'product';
    const LINK_TYPE_SEASON = 'season';
    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const TYPE_PREFIX = 'pcdm_hp_';

    protected $do_not_translate;
    protected $link_types;

    public function __construct() {
        $this->do_not_translate = array(
            'description',
            'hp_link'
        );

        $this->link_types = array(
            array('name' => 'Custom', 'value' => self::LINK_TYPE_CUSTOM),
            array('name' => 'Product', 'value' => self::LINK_TYPE_PRODUCT),
            array('name' => 'Season', 'value' => self::LINK_TYPE_SEASON),
        );

        add_action('init', array(&$this, 'defineType'));
        add_filter('cmb_meta_boxes', array(&$this, 'defineFields'));
        add_filter('pll_copy_post_metas', array(&$this, 'avoidTranslation'));
        //definizione dei nuovi parametri in griglia
        add_filter(sprintf("manage_%s_posts_columns", self::TYPE_IDENTIFIER), array(&$this, 'changeColumns'));
        add_action("manage_posts_custom_column", array(&$this, "fillColumns"), 10, 2);
        
        add_image_size( 'pcdm_hp_home_image_big', 925, 672, FALSE );
        add_image_size( 'pcdm_hp_home_image_medium', 610, 610, FALSE );
        add_image_size( 'pcdm_hp_home_image_small', 305, 305, FALSE );
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
            if (!($key === FALSE)) {
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
            'name' => _x('HP Elements', 'post type general name'),
            'singular_name' => _x('HP Element', 'post type singular name'),
            'add_new' => _x('Add New', 'home item'),
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
                        array('name' => 'Large', 'value' => self::TPL_LARGE),
                        array('name' => 'Medium', 'value' => self::TPL_MEDIUM),
                        array('name' => 'Small', 'value' => self::TPL_SMALL),
                    )
                ),
                array(
                    'name' => 'Void After',
                    'desc' => 'Check if you want a void space after this element',
                    'id' => self::TYPE_PREFIX . 'void_after',
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'Align Left',
                    'desc' => 'Check if you want the image to be displayed on the right of this object',
                    'id' => self::TYPE_PREFIX . 'align_left',
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
                    'id' => self::TYPE_PREFIX . 'hp_number',
                    'type' => 'text_numericint',
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
                    'id' => self::TYPE_PREFIX . 'hp_link',
                    'type' => 'text_medium'
                ),
                array(
                    'name' => 'Link type',
                    'desc' => 'Select the link type',
                    'id' => self::TYPE_PREFIX . 'link_type',
                    'type' => 'radio_inline',
                    'options' => $this->link_types,
                    'std'=>self::LINK_TYPE_CUSTOM
                ),
            ),
        );

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_4',
            'title' => 'Link',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Home Page Image',
                    'desc' => 'Upload an image or enter an URL.',
                    'id' => self::TYPE_PREFIX . 'home_image',
                    'type' => 'file',
                    'save_id' => true, // save ID using true
                    'allow' => array('attachment') // limit to just attachments with array( 'attachment' )
                ),
            ),
        );

        return $meta_boxes;
    }

    public function changeColumns($cols) {

        $new_cols = array(
            self::TYPE_PREFIX . 'hp_template' => __('Template', 'trans'),
            self::TYPE_PREFIX . 'void_after' => __('Space', 'trans'),
            self::TYPE_PREFIX . 'hp_number' => __('Number', 'trans'),
        );
        return array_merge($cols, $new_cols);
    }

    function fillColumns($column, $post_id) {
        switch ($column) {
            case self::TYPE_PREFIX . 'hp_template':
                $template = get_post_meta($post_id, self::TYPE_PREFIX . 'hp_template', true);
                switch ($template) {
                    case self::TPL_LARGE:
                        echo 'large';
                        break;
                    case self::TPL_MEDIUM:
                        echo 'medium';
                        break;
                    case self::TPL_SMALL:
                        echo 'small';
                        break;
                }
                break;
            case self::TYPE_PREFIX . 'void_after':
                $template = get_post_meta($post_id, self::TYPE_PREFIX . 'void_after', true);
                echo $template == 'on' ? 'space' : 'no space';
                break;
            case self::TYPE_PREFIX . 'hp_number':
                $template = get_post_meta($post_id, self::TYPE_PREFIX . 'hp_number', true);
                echo isset($template) && $template != '' ? $template : 'not set';
                break;
        }
    }

}