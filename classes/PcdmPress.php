<?php

class PcdmPress {
  /**
   * Definisce il nome del tipo di dato che utilizziamo
   */

  const TYPE_IDENTIFIER = 'pcdm_press';

  /**
   * Definisce il prefisso per i capi per questo tipo di dato
   */
  const TYPE_PREFIX = 'pcdm_press_';

  public function __construct() {
    add_action('init', array(&$this, 'defineType'));
    add_filter('cmb_meta_boxes', array(&$this, 'defineFields'));
    //registro la callback ajax
    add_action('wp_ajax_nopriv_press', array(&$this, 'pressDetailJsonAction'));
    add_action('wp_ajax_press', array(&$this, 'pressDetailJsonAction'));
    
    add_image_size( 'pcdm_press_wall_image', 221, 296, FALSE );
  }

  public function pressDetailJsonAction() {
    $details = array(
        'filters' => array(),
        'items' => array()
    );

    $filters = array();

    $query = array();

    foreach (self::getDefinedTaxonomies() as $tax_idf) {

      $filters[$tax_idf] = array(
          'filter' => $tax_idf,
          'label' => 'All',
          'list' => array()
      );

      if (isset($_POST[$tax_idf]) && is_numeric($_POST[$tax_idf])) {
        $query[] = array(
            'taxonomy' => $tax_idf,
            'terms' => (int) $_POST[$tax_idf],
            'field' => 'term_id',
        );
        $term = get_term((int) $_POST[$tax_idf],$tax_idf);
        $filters[$tax_idf]['label'] = $term->name;
        $filters[$tax_idf]['list'][0] =array(
            'value'=>'',
            'label'=>'All'
        );
      }
    }

    $args = array(
        'posts_per_page' => '-1',
        'post_type' => PcdmPress::TYPE_IDENTIFIER,
        'tax_query' => $query
    );

    $presses = get_posts($args);

    foreach ($presses as $press) {
      $details['items'][] = (string) $press->ID;
      foreach (self::getDefinedTaxonomies() as $tax_idf) {
        $term_list = wp_get_post_terms($press->ID, $tax_idf, array("fields" => "all"));
        foreach ($term_list as $term) {
          if (!isset($filters[$tax_idf]['list'][$term->term_id])) {
            $filters[$tax_idf]['list'][$term->term_id]['value'] = $term->term_id;
            $filters[$tax_idf]['list'][$term->term_id]['label'] = $term->name;
          }
        }
      }
    }

    foreach($filters as $filter_info){
      $list = array();
      foreach($filter_info['list'] as $list_info){
        $list[]=array(
            'value'=>$list_info['value'],
            'label'=>$list_info['label']
        );
      }
      
      $details['filters'][]=array(
          'filter'=>$filter_info['filter'],
          'label'=>$filter_info['label'],
          'list'=>$list
      );
    }
    
    header("Content-type: application/json");
    die(json_encode($details));
  }

  public static function getDefinedTaxonomies() {
    return array(
        PcdmPressType::CATEGORY_IDENTIFIER,
        PcdmPressNation::CATEGORY_IDENTIFIER,
        PcdmPressYear::CATEGORY_IDENTIFIER,
        PcdmPressNewspaper::CATEGORY_IDENTIFIER,
    );
  }

  /**
   * Definisce il tipo di dato Prodotto da console di amministrazione         
   */
  public function defineType() {

    $labels = array(
        'name' => _x('Press', 'post type general name'),
        'singular_name' => _x('Press', 'post type singular name'),
        'add_new' => _x('Add New', 'home item'),
        'add_new_item' => __('Add Press'),
        'edit_item' => __('Edit Press Item'),
        'new_item' => __('New Press Item'),
        'view_item' => __('View Press Item'),
        'search_items' => __('Search Press'),
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
        'rewrite' => array(
            'slug' => 'press',
            'with_front' => false
        ),
        'capability_type' => 'post',
        'hierarchical' => false, //non presenta gerarchia
        'menu_position' => null,
        'has_archive' => true,
        'supports' => array('title')
    );

    register_post_type(self::TYPE_IDENTIFIER, $args);
  }

  public function defineFields($meta_boxes) {

    $meta_boxes[] = array(
        'id' => self::TYPE_PREFIX . 'fieldset_1',
        'title' => 'Images',
        'pages' => array(self::TYPE_IDENTIFIER),
        'context' => 'normal',
        'priority' => 'low',
        'show_names' => true,
        'fields' => array(
            array(
                'name' => 'Wall Image',
                'desc' => 'Upload an image or enter an URL.',
                'id' => self::TYPE_PREFIX . 'wall_image',
                'type' => 'file',
                'save_id' => true, // save ID using true
                'allow' => array('attachment') // limit to just attachments with array( 'attachment' )
            ),
        ),
    );

    return $meta_boxes;
  }

}