<?php

/*
  Plugin Name: PCDM Products
  Description: To allow creation/modification/other with products
  Version: 1.0
  Author: Antonio Pastorino
 */

//Catch anyone trying to directly acess the plugin - which isn't allowed
if (!function_exists('add_action')) {
    PcdmGenericsPlugin::exitForbidden();
}

//Check if the the class already exists
if (!class_exists("PcdmProductsPlugin")) {

    class PcdmProductsPlugin {

        public function defineProducts() {

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
//                'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',
                'rewrite' => array('slug' => 'products'),
                'capability_type' => 'post',
                'hierarchical' => false, //non presenta gerarchia
                'menu_position' => null,
                'supports' => array('title', 'editor', 'thumbnail')
            );

            register_post_type('pcdm_prodcuts', $args);
        }

    }

}



//PROCEED TO USE THE PLUGIN

if (class_exists("PcdmProductsPlugin")) {
    $pcdmProductsPlugin = new PcdmProductsPlugin();
}

if (isset($pcdmProductsPlugin)) {
    add_action('init', array(&$pcdmProductsPlugin, 'defineProducts'));
}