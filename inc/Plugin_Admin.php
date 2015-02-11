<?php

namespace awis_wc_pf\inc;

abstract class Plugin_Admin
{

    function __construct()
    {
        if (!is_admin()) {
            return;
        }

        //add plugin styles/scripts
        add_action('admin_enqueue_scripts', array($this, 'add_enqueue_scripts'));

        //add admin menu items
        add_action('admin_menu', array($this, 'add_admin_items'));
    }

    /**
     * Add plugin styles/scripts
     * @return mixed
     */
    abstract function add_enqueue_scripts();

    /**
     * Add admin menu items
     * @return mixed
     */
    abstract function add_admin_items();
}