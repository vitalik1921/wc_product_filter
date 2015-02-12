<?php

namespace awis_wc_pf\pbl;

use awis_wc_pf\WC_PF_Loader;

class WC_PF_Public extends \awis_wc_pf\inc\Plugin_Public
{

    /**
     * Add plugin styles/scripts
     * @return mixed
     */
    function add_enqueue_scripts()
    {
        if (!is_post_type_archive('product') && !is_tax(get_object_taxonomies('product')))
            return;

        $enable_ajax = \awis_wc_pf\adm\WC_PF_Admin::get_option('enable_ajax');

        if ($enable_ajax == 'yes') {
            wp_enqueue_style('wc-product-filter', WC_PF_Loader::getPluginUrl() . '/pbl/css/wc-product-filter.css');
            wp_enqueue_script( "wc-product-filter", WC_PF_Loader::getPluginUrl(). '/pbl/js/wc-product-filter.js', array('jquery'));
        }
    }
}