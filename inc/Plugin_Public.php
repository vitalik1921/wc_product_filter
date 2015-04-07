<?php

namespace awis_wc_pf\inc;

abstract class Plugin_Public
{

    function __construct()
    {
        if (is_admin()) {
            return;
        }

        //add plugin styles/scripts
        add_action('wp_enqueue_scripts', array($this, 'add_enqueue_scripts'));
    }

    /**
     * Add plugin styles/scripts
     * @return mixed
     */
    abstract function add_enqueue_scripts();

}