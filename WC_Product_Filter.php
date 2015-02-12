<?php

namespace awis_wc_pf;

class WC_Product_Filter extends \awis_wc_pf\inc\Plugin
{

    function __construct($id, $name, $version)
    {
        parent::__construct($id, $name, $version);

        //Enable filter functionality on WooCommerce
        add_action('init', array($this, 'init_wc_layered_nav'), 99);

        //Init ShortCodes
        add_action('init', array($this, 'initShortCodes'), 100);
    }

    /**
     *  Enable filter functionality on WooCommerce
     */
    function init_wc_layered_nav()
    {
        global $_chosen_attributes, $_attributes_array;

        $_chosen_attributes = $_attributes_array = array();
        $attribute_taxonomies = wc_get_attribute_taxonomies();

        if ($attribute_taxonomies) {
            foreach ($attribute_taxonomies as $tax) {

                $attribute = sanitize_title($tax->attribute_name);
                $taxonomy = wc_attribute_taxonomy_name($attribute);

                // create an array of product attribute taxonomies
                $_attributes_array[] = $taxonomy;

                $name = 'filter_' . $attribute;
                $query_type_name = 'query_type_' . $attribute;

                if (!empty($_GET[$name]) && taxonomy_exists($taxonomy)) {

                    $_chosen_attributes[$taxonomy]['terms'] = explode(',', $_GET[$name]);

                    if (empty($_GET[$query_type_name]) || !in_array(strtolower($_GET[$query_type_name]), array('and', 'or'))) {
                        $_chosen_attributes[$taxonomy]['query_type'] = apply_filters('woocommerce_layered_nav_default_query_type', 'and');
                    } else {
                        $_chosen_attributes[$taxonomy]['query_type'] = strtolower($_GET[$query_type_name]);
                    }

                }
            }
        }

        add_filter('loop_shop_post_in', array(WC()->query, 'layered_nav_query'));
    }

    /**
     *  Init ShortCodes
     */
    function initShortCodes()
    {
        //Add category tree ShortCode
        add_shortcode('wc_pf_cats', array($this, 'getCategoryTreeShortCode'));

        //Add attributes tree ShortCode
        add_shortcode('wc_pf_attrs', array($this, 'getAttributesTreeShortCode'));
    }

    /**
     * Get category tree with links for filter
     */
    function getCategoryTree()
    {
        global $_chosen_attributes;
    }

    /**
     * Get attributes tree with links for filter
     */
    function getAttributesTree()
    {
        global $_chosen_attributes, $wpdb;

        if (!is_post_type_archive('product') && !is_tax(get_object_taxonomies('product')))
            return;

        //we are getting current taxonomy
        $current_term = is_tax() ? get_queried_object()->term_id : '';

        //we are going to store results
        $result = '';

        //we are getting taxonomies
        if ($taxonomies = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies ORDER BY attribute_name ASC")) {
            $result .= "<ul>";
        } else {
            return '';
        }

        foreach ($taxonomies as $taxonomy_object) {

            $taxonomy = $taxonomy_object->attribute_name;

            $result .= "<li class='taxonomy'>$taxonomy</li>";

            //we are creating array with query options
            $get_terms_args = array('hide_empty' => '1');

            $orderby = wc_attribute_orderby($taxonomy);

            switch ($orderby) {
                case 'name' :
                    $get_terms_args['orderby'] = 'name';
                    $get_terms_args['menu_order'] = false;
                    break;
                case 'id' :
                    $get_terms_args['orderby'] = 'id';
                    $get_terms_args['order'] = 'ASC';
                    $get_terms_args['menu_order'] = false;
                    break;
                case 'menu_order' :
                    $get_terms_args['menu_order'] = 'ASC';
                    break;
            }

            //we are getting taxonomy terms
            if ($terms = get_terms('pa_' . $taxonomy, $get_terms_args)) {

                foreach ($terms as $term) {

                    //get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5(sanitize_key($taxonomy) . sanitize_key($term->term_taxonomy_id));

                    //we are checking cache, if its empty then we calc objects in term and set temporary value
                    if (false === ($_products_in_term = get_transient($transient_name))) {
                        $_products_in_term = get_objects_in_term($term->term_id, 'pa_' . $taxonomy);
                        set_transient($transient_name, $_products_in_term);
                    }

                    //define query type
                    $query_type = (isset($_chosen_attributes['pa_' . $taxonomy]['query_type']) ? $_chosen_attributes['pa_' . $taxonomy]['query_type'] : 'and');

                    //get count of product using current filters
                    // If this is an AND query, only show options with count > 0
                    if ($query_type == 'and') {
                        $count = sizeof(array_intersect($_products_in_term, WC()->query->filtered_product_ids));
                        // If this is an OR query, show all options so search can be expanded
                    } else {
                        $count = sizeof(array_intersect($_products_in_term, WC()->query->unfiltered_product_ids));
                    }

                    $arg = 'filter_' . $taxonomy;

                    $current_filter = (isset($_GET[$arg])) ? explode(',', $_GET[$arg]) : array();

                    if (!is_array($current_filter))
                        $current_filter = array();

                    $current_filter = array_map('esc_attr', $current_filter);

                    if (!in_array($term->term_id, $current_filter))
                        $current_filter[] = $term->term_id;

                    // Base Link decided by current page
                    if (defined('SHOP_IS_ON_FRONT')) {
                        $link = home_url();
                    } elseif (is_post_type_archive('product') || is_page(wc_get_page_id('shop'))) {
                        $link = get_post_type_archive_link('product');
                    } else {
                        $link = get_term_link(get_query_var('term'), get_query_var('taxonomy'));
                    }

                    // All current filters
                    if ($_chosen_attributes) {
                        foreach ($_chosen_attributes as $name => $data) {
                            if ($name !== 'pa_' . $taxonomy) {

                                // Exclude query arg for current term archive term
                                while (in_array($current_term, $data['terms'])) {
                                    $key = array_search($current_term, $data);
                                    unset($data['terms'][$key]);
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title(str_replace('pa_', '', $name));

                                if (!empty($data['terms']))
                                    $link = add_query_arg('filter_' . $filter_name, implode(',', $data['terms']), $link);

                                if ($data['query_type'] == 'or')
                                    $link = add_query_arg('query_type_' . $filter_name, 'or', $link);
                            }
                        }
                    }

                    // Orderby
                    if (isset($_GET['orderby']))
                        $link = add_query_arg('orderby', $_GET['orderby'], $link);

                    // Current Filter
                    if (isset($_chosen_attributes['pa_' . $taxonomy]) && is_array($_chosen_attributes['pa_' . $taxonomy]['terms']) && in_array($term->term_id, $_chosen_attributes['pa_' . $taxonomy]['terms'])) {

                        $class = 'class="chosen"';
                        // Remove this term is $current_filter has more than 1 term filtered
                        if (sizeof($current_filter) > 1) {
                            $current_filter_without_this = array_diff($current_filter, array($term->term_id));
                            $link = add_query_arg($arg, implode(',', $current_filter_without_this), $link);
                        }

                    } else {
                        $class = '';
                        $link = add_query_arg($arg, implode(',', $current_filter), $link);
                    }

                    // Search Arg
                    if (get_search_query())
                        $link = add_query_arg('s', get_search_query(), $link);

                    // Post Type Arg
                    if (isset($_GET['post_type']))
                        $link = add_query_arg('post_type', $_GET['post_type'], $link);

                    // Query type Arg
                    if ($query_type == 'or' && !(sizeof($current_filter) == 1 && isset($_chosen_attributes['pa_' . $taxonomy]['terms']) && is_array($_chosen_attributes['pa_' . $taxonomy]['terms']) && in_array($term->term_id, $_chosen_attributes['pa_' . $taxonomy]['terms'])))
                        $link = add_query_arg('query_type_' . $taxonomy, 'or', $link);


                    $result .= "<li data-count='$count' $class><a href='$link'>{$term->name}</a></li>";
                }
            }
        }

        $result .= "</ul>";
        return $result;
    }


    /**
     * Get category tree - ShortCode
     * @param $atts
     * @param string $content
     */
    function getCategoryTreeShortCode($atts, $content = "")
    {
        $this->getCategoryTree();
    }

    /**
     * Get attributes tree - ShortCode
     * @param $atts
     * @param string $content
     */
    function getAttributesTreeShortCode($atts, $content = "")
    {
        $this->getAttributesTree();
    }

}