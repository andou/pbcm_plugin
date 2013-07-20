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

    protected $do_not_translate;

    public function __construct() {
        $this->do_not_translate = array(
            'description'
        );
        add_action('init', array(&$this, 'defineType'));
        add_action('before_delete_post', array(&$this, 'delete'));
        add_filter('cmb_meta_boxes', array(&$this, 'defineFields'));
        //definizione dei nuovi parametri in griglia
        add_filter(sprintf("manage_%s_posts_columns", self::TYPE_IDENTIFIER), array(&$this, 'changeColumns'));
        add_filter('pll_copy_post_metas', array(&$this, 'avoidTranslation'));
        add_action("manage_posts_custom_column", array(&$this, "fillColumns"), 10, 2);
        //registro la callback ajax
        add_action('wp_ajax_nopriv_product_details', array(&$this, 'productDetailsJsonAction'));
        add_action('wp_ajax_product_details', array(&$this, 'productDetailsJsonAction'));
           
        add_filter('post_type_link', array(&$this, 'customPermalink'), 10, 3);
        
    }
    
    
    
  public function customPermalink($permalink, $post_id, $leavename) {
    if (strpos($permalink, '%prod_season%') === FALSE)
      return $permalink;
    
    if (strpos($permalink, '%prod_id%') === FALSE)
      return $permalink;

    // Get post
    $post = get_post($post_id);
    if (!$post)
      return $permalink;

    // Get taxonomy terms
    $terms = wp_get_object_terms($post->ID, PcdmSeason::CATEGORY_IDENTIFIER);
    if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0]))
      $taxonomy_slug = $terms[0]->slug;
    else
      $taxonomy_slug = 'no-season';

    $permalink = str_replace('%prod_season%', $taxonomy_slug, $permalink); 
    
    $permalink = str_replace('%prod_id%', $post->ID, $permalink);
    
    return $permalink;
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
            'rewrite' => false,//array('slug' => 'products'),
            'capability_type' => 'post',
            'hierarchical' => false, //non presenta gerarchia
            'menu_position' => null,
            'supports' => array('title', 'thumbnail')
        );

        register_post_type(self::TYPE_IDENTIFIER, $args);
        
        global $wp_rewrite;
        $product_structure = 'seasons/%prod_season%/%prod_id%/';
        $rewrite_args = array(
          'with_front'=>false,  
        );
        $wp_rewrite->add_rewrite_tag("%prod_id%", '([^/]+)', "post_type=pcdm_products&p=");
        $wp_rewrite->add_rewrite_tag("%prod_season%", '([^/]+)', "category_name=");
        
        $wp_rewrite->add_permastruct('pcdm_products', $product_structure,$rewrite_args);
//        $wp_rewrite->flush_rules();
    }

    /**
     * Restituisce un array di prodotti da visualizzarsi nel selettore dei
     * prodotti 
     * 
     * @param string $orderBy
     * @param string $orderIn
     * @return array
     */
    public static function getProductsForSelection($orderBy = 'title', $orderIn = 'ASC') {

        $products = array();

        $args = array(
            'post_type' => self::TYPE_IDENTIFIER,
            'post_status' => 'publish',
            'orderby' => $orderBy,
            'order' => $orderIn,
        );

        foreach (get_posts($args) as $product) {
            $products[] = array(
                'name' => $product->post_title,
                'value' => $product->ID
            );
        }


        return $products;
    }

    /**
     * Definisce i campi per questo TDD da mostrarsi a console di admin
     * 
     * @param type $meta_boxes
     * @return boolean
     */
    public function defineFields($meta_boxes) {
        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_1',
            'title' => 'Description',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Description',
                    'desc' => 'Insert a description for this product',
                    'id' => self::TYPE_PREFIX . 'description',
                    'type' => 'textarea_small'
                ),
            ),
        );

        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_2',
            'title' => 'Appearance',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Number',
                    'desc' => 'Define the number of this element',
                    'id' => self::TYPE_PREFIX . 'number',
                    'type' => 'text_numericint'
                ),
                array(
                    'name' => 'Color',
                    'desc' => 'Pick a color for the hover',
                    'id' => self::TYPE_PREFIX . 'collection_color',
                    'type' => 'colorpicker'
                ),
                array(
                    'name' => 'Text',
                    'desc' => 'Check if you want the hover text to be white, otherwise it\'ll be dark',
                    'id' => self::TYPE_PREFIX . 'text_color',
                    'type' => 'checkbox'
                ),
            ),
        );
        $meta_boxes[] = array(
            'id' => self::TYPE_PREFIX . 'fieldset_3',
            'title' => 'Images',
            'pages' => array(self::TYPE_IDENTIFIER),
            'context' => 'normal',
            'priority' => 'low',
            'show_names' => true,
            'fields' => array(
                array(
                    'name' => 'Detail image',
                    'desc' => 'Upload an image or enter an URL.',
                    'id' => self::TYPE_PREFIX . 'detail_image',
                    'type' => 'file',
                    'save_id' => false, // save ID using true
                    'allow' => array('url', 'attachment') // limit to just attachments with array( 'attachment' )
                ),
                array(
                    'name' => 'Collection Big Image',
                    'desc' => 'Upload an image or enter an URL.',
                    'id' => self::TYPE_PREFIX . 'collection_image_big',
                    'type' => 'file',
                    'save_id' => false, // save ID using true
                    'allow' => array('url', 'attachment') // limit to just attachments with array( 'attachment' )
                ),
                array(
                    'name' => 'Collection Small Image',
                    'desc' => 'Upload an image or enter an URL.',
                    'id' => self::TYPE_PREFIX . 'collection_image_small',
                    'type' => 'file',
                    'save_id' => false, // save ID using true
                    'allow' => array('url', 'attachment') // limit to just attachments with array( 'attachment' )
                ),
            ),
        );

        return $meta_boxes;
    }

    /**
     * Definisce la grid di questo TDD
     * 
     * @param type $cols
     * @return type
     */
    public function changeColumns($cols) {

        $new_cols = array(
            self::TYPE_PREFIX . 'collection_color' => __('Hover Color', 'trans'),
        );
        return array_merge($cols, $new_cols);
    }

    /**
     * Definisce come riempire la grid di questo TDD
     * 
     * @param type $column
     * @param type $post_id
     */
    function fillColumns($column, $post_id) {
        switch ($column) {
            case self::TYPE_PREFIX . 'collection_color':
                $color = get_post_meta($post_id, self::TYPE_PREFIX . 'collection_color', true);
                echo sprintf("<span style=\"color:%s;font-weight:bold;\">%s</span>", $color, $color);
                break;
        }
    }

    /**
     * Hook/observer per la cancellazione di un oggetto
     * 
     * @global type $post_type
     * @param type $postid
     * @return type
     */
    public function delete($postid) {
        global $post_type;
        if ($post_type != self::TYPE_IDENTIFIER)
            return;

        $args = array(
            'post_type' => PcdmProductBucket::TYPE_IDENTIFIER
        );
        foreach (get_post($args) as $postinfo) {
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_a', $postid);
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_b', $postid);
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_c', $postid);
            delete_post_meta($postinfo->ID, PcdmProductBucket::TYPE_PREFIX . 'prod_d', $postid);
        }
    }

    /**
     * Gestisce la callback ajax per la richiesta delle informazioni relative ad
     * un prodotto a catalogo
     * 
     */
    public function productDetailsJsonAction() {
        $details = array(
            'details' => array()
        );
        if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
            $_pid = (int) $_POST['product_id'];
            $product = get_post($_pid);
            if ($product->ID) {
                if ($product->post_type == self::TYPE_IDENTIFIER) {
                    $meta = get_post_meta($_pid);


                    $details['details']['img'] = $meta[self::TYPE_PREFIX . 'detail_image'][0];
                    $details['details']['number'] = $meta[self::TYPE_PREFIX . 'number'][0];

                    $seasons = wp_get_post_terms($_pid, PcdmSeason::CATEGORY_IDENTIFIER);
                    if (count($seasons)) {
                        $season = array_pop($seasons);
                        $details['details']['collection'] = $season->name;
                    }

                    $details['details']['title'] = $product->post_title;
                    $details['details']['description'] = $meta[self::TYPE_PREFIX . 'description'][0];
                    $details['details']['social'] = array();
                }
            }
        }

        header("Content-type: application/json");
        die(json_encode($details));
    }

}
