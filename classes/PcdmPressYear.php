<?php

class PcdmPressYear {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const CATEGORY_IDENTIFIER = 'pcdm_pressyear';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const CATEGORY_PREFIX = 'pcdm_pressyear_';

    public function __construct() {
        //definizione della tassonomia
        add_action('init', array(&$this, 'registerTaxonomy'));
    }

    public function registerTaxonomy() {
        $labels = array(
            'name' => _x('Year', 'taxonomy general name'),
            'singular_name' => _x('Press Year', 'taxonomy singular name'),
            'search_items' => __('Search a Press Year'),
            'all_items' => __('All Press Years'),
            'edit_item' => __('Edit Press Year Info'),
            'update_item' => __('Update Press Year'),
            'add_new_item' => __('Add New Press Year'),
            'new_item_name' => __('New Press Year'),
        );

        $types = array(
            PcdmPress::TYPE_IDENTIFIER
        );
        
        $args = array(
            "hierarchical" => true,
            "labels" => $labels,
            'sort' => true,
            'rewrite' => array('slug' => 'press_year'),
        );
        register_taxonomy(self::CATEGORY_IDENTIFIER, $types, $args);
    }

}

