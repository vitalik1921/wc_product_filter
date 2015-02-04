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
        return;
    }
}

$wc_pf_loader = new WC_PF_Loader(plugin_dir_path(__FILE__), '\awis_wc_pf\WC_Product_Filter', '\awis_wc_pf\pbl\WC_PF_Public', '\awis_wc_pf\adm\WC_PF_Admin', 0, 'WC Product Filter', '1.0.0');

//register WP hooks
register_activation_hook(__FILE__, array($wc_pf_loader, 'activation'));
register_deactivation_hook(__FILE__, array($wc_pf_loader, 'deactivation'));
