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
////////////////////////////  HP ELEMENTS  /////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmHomeElement")) {
  include_once dirname(__FILE__) . '/classes/PcdmHomeElement.php';
}

if (class_exists("PcdmHomeElement")) {
  new PcdmHomeElement();
}

////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////  NEWS  /////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmNews")) {
  include_once dirname(__FILE__) . '/classes/PcdmNews.php';
}

if (class_exists("PcdmNews")) {
  new PcdmNews();
}

////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////  PRESS  ////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmPress")) {
  include_once dirname(__FILE__) . '/classes/PcdmPress.php';
}

if (class_exists("PcdmPress")) {
  new PcdmPress();
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////  PRESS CATEGORIES /////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmPressYear")) {
  include_once dirname(__FILE__) . '/classes/PcdmPressYear.php';
}

if (class_exists("PcdmPressYear")) {
  new PcdmPressYear();
}


if (!class_exists("PcdmPressNewspaper")) {
  include_once dirname(__FILE__) . '/classes/PcdmPressNewspaper.php';
}

if (class_exists("PcdmPressNewspaper")) {
  new PcdmPressNewspaper();
}


if (!class_exists("PcdmPressNation")) {
  include_once dirname(__FILE__) . '/classes/PcdmPressNation.php';
}

if (class_exists("PcdmPressNation")) {
  new PcdmPressNation();
}

if (!class_exists("PcdmPressType")) {
  include_once dirname(__FILE__) . '/classes/PcdmPressType.php';
}

if (class_exists("PcdmPressType")) {
  new PcdmPressType();
}

////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////  SEASON//  ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


if (!class_exists("PcdmSeason")) {
  include_once dirname(__FILE__) . '/classes/PcdmSeason.php';
}

if (class_exists("PcdmSeason")) {
  new PcdmSeason();
}


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

/////////////////METABOX DEFINITION  ///////////////////////////////////////////
//numeric
add_action('cmb_render_text_numericint', 'rrh_cmb_render_text_numericint', 10, 2);

function rrh_cmb_render_text_numericint($field, $meta) {
  if ($meta !== "0") {
    $value = !(empty($meta)) ? $meta : $field['std'];
  } else {
    $value = $meta;
  }

  echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $value, '" style="width:97%" />', '<p class="cmb_metabox_description">', $field['desc'], '</p>';
}

add_filter('cmb_validate_text_numericint', 'rrh_cmb_validate_text_numericint');

function rrh_cmb_validate_text_numericint($new) {
  $new = (int) $new;
  if ($new === 0) {
    return "0";
  }
  return (int) $new;
}

//stili e js per la console di admin
add_action('admin_menu', 'pcdm_scripts_admin_styles');

function pcdm_scripts_admin_styles() {
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_style('pcdm-admin-css', plugin_dir_url(__FILE__) . 'skin/css/pcdm-admin.css');
  wp_enqueue_script('pcdm-admin-script', plugin_dir_url(__FILE__) . 'skin/js/pcdm-admin.js', array('jquery'));
}

add_filter('got_rewrite', '__return_true', 999);


//callback registrazione newsletter

add_action('wp_ajax_nopriv_registernl', 'registerNewsletter');
add_action('wp_ajax_registernl', 'registerNewsletter');

function registerNewsletter() {

  $res = array();
  $success = 1;
  $res['error'] = array();

  if (!isset($_POST['name'])) {
    $success = 0;
    $res['error'][] = 'name';
  }

  if (!isset($_POST['surname'])) {
    $success = 0;
    $res['error'][] = 'surname';
  }

  if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $success = 0;
    $res['error'][] = 'email';
  }

  $res['success'] = $success;

  header("Content-type: application/json");
  die(json_encode($res));
}