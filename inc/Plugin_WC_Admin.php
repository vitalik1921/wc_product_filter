<?php

namespace awis_wc_pf\inc;

class Plugin_WC_Admin
{
    /** PROTECTED   */
    public static $tab_id;
    public static $tab_name;
    public static $settings = array();

    function __construct($tab_id = 'settings_tab_default', $tab_name = 'Settings tab', $settings)
    {
        self::$tab_id = $tab_id;
        self::$tab_name = $tab_name;
        self::$settings = $settings;

        if (!is_admin()) {
            return;
        }

        //add WooCommerce tab
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);

        //add WooCommerce settings
        add_action('woocommerce_settings_tabs_' . self::$tab_id, function () {
            woocommerce_admin_fields(self::get_settings());
        });

        //update WooCommerce settings
        add_action('woocommerce_update_options_' . self::$tab_id, function () {
            woocommerce_update_options(self::get_settings());
        });
    }

    /**
     * Create tab for WooCommerce settings
     * @param $settings_tabs
     * @return mixed
     */
    function add_settings_tab($settings_tabs)
    {
        $settings_tabs[self::$tab_id] = self::$tab_name;
        return $settings_tabs;
    }

    /**
     * Init settings
     * @return mixed|void
     */
    static function get_settings()
    {
        return apply_filters('wc_settings_tab_' . self::$tab_id, self::$settings);
    }

    /**
     * Get option by id
     * @param $key
     * @return mixed|void
     */
    static function get_option( $key ) {
        $fields = self::$settings;

        return apply_filters( 'wc_option_' . $key, get_option( 'wc_settings_' . self::$tab_id . '_' . $key, ( ( isset( $fields[$key] ) && isset( $fields[$key]['default'] ) ) ? $fields[$key]['default'] : '' ) ) );
    }
}