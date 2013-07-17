<?php

class PcdmPressNation{
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const CATEGORY_IDENTIFIER = 'pcdm_pressnation';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const CATEGORY_PREFIX = 'pcdm_pressnation_';

    public function __construct() {
        //definizione della tassonomia
        add_action('init', array(&$this, 'registerTaxonomy'));
    }

    public function registerTaxonomy() {
        $labels = array(
            'name' => _x('Nation', 'taxonomy general name'),
            'singular_name' => _x('Press Nation', 'taxonomy singular name'),
            'search_items' => __('Search a Press Nation'),
            'all_items' => __('All Press Nations'),
            'edit_item' => __('Edit Press Nation Info'),
            'update_item' => __('Update Press Nation'),
            'add_new_item' => __('Add New Press Nation'),
            'new_item_name' => __('New Press Nation'),
        );

        
        $types = array(
            PcdmPress::TYPE_IDENTIFIER
        );
        
        
        $args = array(
            "hierarchical" => true,
            "labels" => $labels,
            'sort' => true,
            'rewrite' => array('slug' => 'press_nation'),
        );
        register_taxonomy(self::CATEGORY_IDENTIFIER, $types, $args);
    }

}

