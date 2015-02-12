<?php

namespace awis_wc_pf\adm;

class WC_PF_Admin extends \awis_wc_pf\inc\Plugin_WC_Admin
{
    function __construct()
    {
        global $wpdb;

        $tab_id = 'wc_pf_options';
        $tab_name = 'Filters';

        //bacis settings
        $settings = array(
            'section_general' => array(
                'name' => 'General',
                'type' => 'title',
                'desc' => '',
                'id'   => 'wc_settings_' . $tab_id . '_general'
            ),
            'enable_ajax' => array(
                'name'    => 'Enable Ajax',
                'type'    => 'checkbox',
                'desc'    => 'Choose this to enable AJAX filters.',
                'id'      => 'wc_settings_' . $tab_id . '_enable_ajax',
                'default' => '0',
            ),
            'show_amount' => array(
                'name'    => 'Show amount',
                'type'    => 'checkbox',
                'desc'    => 'Show amount of items.',
                'id'      => 'wc_settings_' . $tab_id . 'show_amount',
                'default' => '0',
            ),
            'section_general_end'   => array(
                'type' => 'sectionend',
                'id'   => 'wc_settings_' . $tab_id . '_general_end'
            )
        );

        //taxonomies
        if ($taxonomies = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies ORDER BY attribute_name ASC")) {

            $settings['section_taxonomies'] = array(
                'name' => 'Taxonomies',
                'type' => 'title',
                'desc' => '',
                'id'   => 'wc_settings_' . $tab_id . '_taxonomies'
            );

            foreach ($taxonomies as $taxonomy_object) {
                $taxonomy_label = $taxonomy_object->attribute_label;
                $taxonomy_id = $taxonomy_object->attribute_name;

                $settings[$taxonomy_id] = array(
                    'name'    => $taxonomy_label,
                    'type'    => 'checkbox',
                    'desc'    => 'Hide',
                    'id'      => 'wc_settings_' . $tab_id . '_'.$taxonomy_id,
                    'default' => '0',
                );
            }

            $settings['section_taxonomies_end'] = array(
                'type' => 'sectionend',
                'id'   => 'wc_settings_' . $tab_id . '_taxonomies_end'
            );
        }

        parent::__construct($tab_id, $tab_name, $settings);
    }
}