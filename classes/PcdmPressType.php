<?php

class PcdmPressType {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const CATEGORY_IDENTIFIER = 'pcdm_presstype';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const CATEGORY_PREFIX = 'pcdm_presstype_';

    public function __construct() {
        //definizione della tassonomia
        add_action('init', array(&$this, 'registerTaxonomy'));
    }

    public function registerTaxonomy() {
        $labels = array(
            'name' => _x('Type', 'taxonomy general name'),
            'singular_name' => _x('Press Type', 'taxonomy singular name'),
            'search_items' => __('Search a Press Type'),
            'all_items' => __('All Press Types'),
            'edit_item' => __('Edit Press Type Info'),
            'update_item' => __('Update Press Type'),
            'add_new_item' => __('Add New Press Type'),
            'new_item_name' => __('New Press Type'),
        );

       
        $types = array(
            PcdmPress::TYPE_IDENTIFIER
        );
        
        $args = array(
            "hierarchical" => true,
            "labels" => $labels,
            'sort' => true,
            'rewrite' => array('slug' => 'press_type'),
        );
        register_taxonomy(self::CATEGORY_IDENTIFIER, $types, $args);
    }

}

