<?php

class PcdmPressNewspaper {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const CATEGORY_IDENTIFIER = 'pcdm_pressnewspaper';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const CATEGORY_PREFIX = 'pcdm_pressnewspaper_';

    public function __construct() {
        //definizione della tassonomia
        add_action('init', array(&$this, 'registerTaxonomy'));
    }

    public function registerTaxonomy() {
        $labels = array(
            'name' => _x('Newspaper', 'taxonomy general name'),
            'singular_name' => _x('Press Newspaper', 'taxonomy singular name'),
            'search_items' => __('Search a Press Newspaper'),
            'all_items' => __('All Press Newspapers'),
            'edit_item' => __('Edit Press Newspaper Info'),
            'update_item' => __('Update Press Newspaper'),
            'add_new_item' => __('Add New Press Newspaper'),
            'new_item_name' => __('New Press Newspaper'),
        );

        
        $types = array(
            PcdmPress::TYPE_IDENTIFIER
        );
        
        
        $args = array(
            "hierarchical" => true,
            "labels" => $labels,
            'sort' => true,
            'rewrite' => array('slug' => 'press_newspaper'),
        );
        register_taxonomy(self::CATEGORY_IDENTIFIER, $types, $args);
    }

}

