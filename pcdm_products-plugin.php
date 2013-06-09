<?php

/*
  Plugin Name: PCDM Products
  Description: To allow creation/modification/other with products
  Version: 1.0
  Author: Antonio Pastorino
 */

//Catch anyone trying to directly acess the plugin - which isn't allowed
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////  PRODUCTS  ////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//Check if PcdmProduct alredy exists
if (!class_exists("PcdmProduct")) {
    include_once dirname(__FILE__) . '/classes/PcdmProduct.php';
}

if (class_exists("PcdmProduct")) {
    new PcdmProduct();
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////  PRODUCT BUCKETS///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmProductBucket")) {
    include_once dirname(__FILE__) . '/classes/PcdmProductBucket.php';
}

if (class_exists("PcdmProductBucket")) {
    new PcdmProductBucket();
}

////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////  SEASON//  ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmSeason")) {
    include_once dirname(__FILE__) . '/classes/PcdmSeason.php';
}

if (class_exists("PcdmProductBucket")) {
    new PcdmSeason();
}

//function be_sample_metaboxes( $meta_boxes ) {
//	$meta_boxes[] = array(
//            'id' => PcdmProduct::TYPE_PREFIX .'fieldset_1',
//            'title' => 'Appearance',
//            'pages' => array(PcdmProduct::TYPE_IDENTIFIER),
//            'context' => 'normal',
//            'priority' => 'low',
//            'show_names' => true,
//            'fields' => array(
//                array(
//                    'name' => 'Color',
//                    'desc' => 'Pick a color for the hover',
//                    'id' => PcdmProduct::TYPE_PREFIX . 'collection_color',
//                    'type' => 'colorpicker'
//                ),
//            ),
//        );
//
//	return $meta_boxes;
//}
//add_filter( 'cmb_meta_boxes', 'be_sample_metaboxes' );

////////////////////////////////////////////////////////////////////////////////
////////////////////////////  OTHER  ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// Initialize the metaboxes class
add_action('init', 'be_initialize_cmb_meta_boxes', 9999);

function be_initialize_cmb_meta_boxes() {
    if (!class_exists('cmb_Meta_Box')) {
        require_once( 'lib/metabox/init.php' );
    }
}

add_action('admin_menu', 'pcdm_scripts_admin_styles');

function pcdm_scripts_admin_styles() {
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_style('pcdm-admin-css', plugin_dir_url(__FILE__) . 'skin/css/pcdm-admin.css');
    wp_enqueue_script('pcdm-admin-script', plugin_dir_url(__FILE__) . 'skin/js/pcdm-admin.js', array('jquery'));
}
