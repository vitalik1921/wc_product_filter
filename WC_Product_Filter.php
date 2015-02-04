<?php

namespace awis_wc_pf;

class WC_Product_Filter extends \awis_wc_pf\inc\Plugin
{

    function __construct($id, $name, $version)
    {
        parent::__construct($id, $name, $version);

        //Enable filter functionality on WooCommerce
        add_action( 'init', array( $this, 'init_wc_layered_nav' ), 99 );

        //Init ShortCodes
        add_action( 'init', array( $this, 'initShortCodes' ), 100 );
    }

    /**
     *  Enable filter functionality on WooCommerce
     */
    function init_wc_layered_nav() {
        global $_chosen_attributes, $woocommerce, $_attributes_array;

        $_chosen_attributes = $_attributes_array = array();

        /* FIX TO WOOCOMMERCE 2.1 */
        if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
            $attribute_taxonomies = wc_get_attribute_taxonomies();
        }
        else {
            $attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
        }

        if ( $attribute_taxonomies ) {
            foreach ( $attribute_taxonomies as $tax ) {

                $attribute = sanitize_title( $tax->attribute_name );

                /* FIX TO WOOCOMMERCE 2.1 */
                if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
                    $taxonomy = wc_attribute_taxonomy_name( $attribute );
                }
                else {
                    $taxonomy = $woocommerce->attribute_taxonomy_name( $attribute );
                }


                // create an array of product attribute taxonomies
                $_attributes_array[] = $taxonomy;

                $name            = 'filter_' . $attribute;
                $query_type_name = 'query_type_' . $attribute;

                if ( ! empty( $_GET[$name] ) && taxonomy_exists( $taxonomy ) ) {

                    $_chosen_attributes[$taxonomy]['terms'] = explode( ',', $_GET[$name] );

                    if ( empty( $_GET[$query_type_name] ) || ! in_array( strtolower( $_GET[$query_type_name] ), array( 'and', 'or' ) ) ) {
                        $_chosen_attributes[$taxonomy]['query_type'] = apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
                    }
                    else {
                        $_chosen_attributes[$taxonomy]['query_type'] = strtolower( $_GET[$query_type_name] );
                    }

                }
            }
        }

        if ( version_compare( preg_replace( '/-beta-([0-9]+)/', '', $woocommerce->version ), '2.1', '<' ) ) {
            add_filter( 'loop_shop_post_in', 'woocommerce_layered_nav_query' );
        }
        else {
            add_filter( 'loop_shop_post_in', array( WC()->query, 'layered_nav_query' ) );
        }
    }

    /**
     *  Init ShortCodes
     */
    function initShortCodes() {
        //Add category tree ShortCode
        add_shortcode('wc_pf_cats', array($this, 'getCategoryTreeShortCode'));

        //Add attributes tree ShortCode
        add_shortcode('wc_pf_attrs', array($this, 'getAttributesTreeShortCode'));
    }

    /**
     * Get category tree with links for filter
     */
    function getCategoryTree() {

    }

    /**
     * Get attributes tree with links for filter
     */
    function getAttributesTree() {

    }

    /**
     * Get category tree - ShortCode
     * @param $atts
     * @param string $content
     */
    function getCategoryTreeShortCode($atts, $content = "") {

    }

    /**
     * Get attributes tree - ShortCode
     * @param $atts
     * @param string $content
     */
    function getAttributesTreeShortCode($atts, $content = "") {

    }

}