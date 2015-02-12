<?php

namespace awis_wc_pf\inc;

class Plugin_WC_Admin
{
    /** PROTECTED   */
    protected $tab_id;
    protected $tab_name;
    protected $settings = array();

    function __construct($tab_id = 'settings_tab_default', $tab_name = 'Settings tab', $settings)
    {
        if (!is_admin()) {
            return;
        }

        $this->tab_id = $tab_id;
        $this->tab_name = $tab_name;
        $this->settings = $settings;

        //add WooCommerce tab
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);

        //add WooCommerce settings
        add_action('woocommerce_settings_tabs_' . $this->tab_id, function () {
            woocommerce_admin_fields($this->get_settings());
        });

        //update WooCommerce settings
        add_action('woocommerce_update_options_' . $this->tab_id, function () {
            woocommerce_update_options($this->get_settings());
        });
    }

    /**
     * Create tab for WooCommerce settings
     * @param $settings_tabs
     * @return mixed
     */
    function add_settings_tab($settings_tabs)
    {
        $settings_tabs[$this->tab_id] = $this->tab_name;
        return $settings_tabs;
    }

    /**
     * Init settings
     * @return mixed|void
     */
    function get_settings()
    {
        return apply_filters('wc_settings_tab_'.$this->tab_id, $this->settings);
    }

    /**
     * Get option by id
     * @param $key
     * @return mixed|void
     */
    public function get_option( $key ) {
        $fields = $this->get_fields();

        return apply_filters( 'wc_option_' . $key, get_option( 'wc_settings_' . $this->tab_id . '_' . $key, ( ( isset( $fields[$key] ) && isset( $fields[$key]['default'] ) ) ? $fields[$key]['default'] : '' ) ) );
    }
}