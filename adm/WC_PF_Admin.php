<?php

namespace awis_wc_pf\adm;

class WC_PF_Admin extends \awis_wc_pf\inc\Plugin_Admin
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add admin menu items
     */
    function add_admin_items() {
        //add option page
        add_options_page( 'Test Plugin','Test plugin options','manage_options','options_page_slug', array($this, 'get_option_page') );
    }

    /**
     * Add plugin styles/scripts
     * @return mixed
     */
    function add_enqueue_scripts()
    {
        // TODO: Implement add_enqueue_scripts() method.
    }

    /**
     *  Add option page
     */
    function get_option_page() {
        // TODO: Implement get_option_page() method.
    }

}