<?php

namespace awis_wc_pf\adm;

class WC_PF_Admin extends \awis_wc_pf\inc\Plugin_WC_Admin
{
    function __construct()
    {
        $tab_id = 'wc_pf_options';
        $tab_name = 'Filters';

        $settings = array(
            'section_title' => array(
                'name' => __( 'An example title', 'example-text-domain' ),
                'type' => 'title',
                'desc' => '',
                'id'   => 'wc_settings_' . $tab_id . '_title'
            ),
            'example_input' => array(
                'name'    => __( 'Example input', 'example-text-domain' ),
                'type'    => 'text',
                'desc'    => __( 'This is an example field, nothing much I can say about it.', 'example-text-domain' ),
                'id'      => 'wc_settings_' . $tab_id . '_example_input',
                'default' => '10',
            ),
            'section_end'   => array(
                'type' => 'sectionend',
                'id'   => 'wc_settings_' . $tab_id . '_section_end'
            )
        );

        parent::__construct($tab_id, $tab_name, $settings);
    }


}