<?php
/**
 * Plugin Name: WC Product Filter
 */

namespace awis_wc_pf;

if (!defined('WPINC')) exit; // Exit if accessed directly

//load plugin loader
require_once('inc/Plugin_Loader.php');

class WC_PF_Loader extends \awis_wc_pf\inc\Plugin_Loader
{
    function __construct($plugin_dir)
    {
        parent::__construct($plugin_dir);

        self::$plugin = new \awis_wc_pf\WC_Product_Filter(0, 'WC Product Filter', '1.0.0');
        self::$plugin_public = new \awis_wc_pf\pbl\WC_PF_Public();
        self::$plugin_admin = new \awis_wc_pf\adm\WC_PF_Admin();
    }

    /**
     * Activation
     */
    public function activation()
    {
        if (!class_exists('WooCommerce')) {
            wp_die('WooCommerce is not installed/activated.');
        }
    }

    /**
     * Deactivation
     */
    public function deactivation()
    {
//        return;
    }
}

$wc_pf_loader = new WC_PF_Loader(plugin_dir_path(__FILE__));

//register WP hooks
register_activation_hook(__FILE__, array($wc_pf_loader, 'activation'));
register_deactivation_hook(__FILE__, array($wc_pf_loader, 'deactivation'));
