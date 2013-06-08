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
    include_once dirname( __FILE__ ) . '/classes/PcdmProduct.php';
}

if (class_exists("PcdmProduct")) {
    $pcdmProduct = new PcdmProduct();
}

if (isset($pcdmProduct)) {
    add_action('init', array(&$pcdmProduct, 'defineType'));
    add_action('add_meta_boxes', array(&$pcdmProduct, 'defineFields'));
    add_action('save_post', array(&$pcdmProduct, 'save'));
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////  PRODUCT BUCKETS///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmProductBucket")) {
    include_once dirname( __FILE__ ) . '/classes/PcdmProductBucket.php';
}

if (class_exists("PcdmProductBucket")) {
    $pcdmProductBucket = new PcdmProductBucket();
}

if (isset($pcdmProductBucket)) {
    add_action('init', array(&$pcdmProductBucket, 'defineType'));
    add_action('cmb_meta_boxes', array(&$pcdmProductBucket, 'defineFields'));
    add_action('save_post', array(&$pcdmProductBucket, 'save'));
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////  OTHER  ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// Initialize the metaboxes class
add_action( 'init', 'be_initialize_cmb_meta_boxes', 9999 );
function be_initialize_cmb_meta_boxes() {
	if ( !class_exists( 'cmb_Meta_Box' ) ) {
		require_once( 'lib/metabox/init.php' );
	}
}


