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
        add_action('add_meta_boxes', array(&$this, 'defineFields'));
        add_action('save_post', array(&$this, 'save'));
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
            'supports' => array('title', 'editor', 'thumbnail')
        );

        register_post_type(self::TYPE_IDENTIFIER, $args);
    }

    public function defineFields() {
        add_meta_box('creation-year-id', 'Creation Year', array(&$this, 'defineCreationYear'), self::TYPE_IDENTIFIER, 'side', 'low');
    }

    public function defineCreationYear() {
        global $post;
        $custom = get_post_custom($post->ID);
        $creation_year = $custom["creation_year"][0];
        ?>  
        <label for="creation_year_text">Creation Year</label>  
        <input type="text" name="creation_year" value="<?= $creation_year ?>" id="creation_year" />  
        <?php
    }

    public function saveDetails() {
        global $post;
        update_post_meta($post->ID, "creation_year", $_POST["creation_year"]);
    }

    public function save($product_id) {

        // Bail if we're doing an auto save  
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // if our current user can't edit this post, bail  
        if (!current_user_can('edit_post'))
            return;

        global $post;
        $post_id = $post->ID;

        // now we can actually save the data  
        $allowed = array(
            'a' => array(// on allow a tags  
                'href' => array() // and those anchors can only have href attribute  
            )
        );

        if (isset($_POST['creation_year']))
            update_post_meta($post_id, 'creation_year', wp_kses($_POST['creation_year'], $allowed));
    }

}