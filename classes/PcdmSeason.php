<?php

class PcdmSeason {
    /**
     * Definisce il nome del tipo di dato che utilizziamo
     */

    const CATEGORY_IDENTIFIER = 'pcdm_season';

    /**
     * Definisce il prefisso per i capi per questo tipo di dato
     */
    const CATEGORY_PREFIX = 'pcdm_ss_';

    public function __construct() {
        //definizione della tassonomia
        add_action('init', array(&$this, 'registerTaxonomy'));
    }

    public function registerTaxonomy() {
        $labels = array(
            'name' => _x('Seasons', 'taxonomy general name'),
            'singular_name' => _x('Season', 'taxonomy singular name'),
            'search_items' => __('Search a Season'),
            'all_items' => __('All Seasons'),
            'edit_item' => __('Edit Season Info'),
            'update_item' => __('Update Season'),
            'add_new_item' => __('Add New Season'),
            'new_item_name' => __('New Season'),
        );

        $types = array(
            PcdmProduct::TYPE_IDENTIFIER,
            PcdmProductBucket::TYPE_IDENTIFIER
        );
        
        $args = array(
        "hierarchical" => TRUE,
            "labels" => $labels,
            'sort' => true,
        'show_ui' => TRUE,
        'query_var' => true,
            'rewrite' => array('slug' => 'seasons'),
        );
        register_taxonomy(self::CATEGORY_IDENTIFIER, $types, $args);
    }

}

