<?php

namespace awis_wc_pf\inc;

abstract class Plugin
{

    /** PROTECTED   */
    protected $id;
    protected $name;
    protected $version;

    function __construct($id, $name, $version)
    {
        //basic meta-data
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Get ID of the plugin
     * @return string
     */
    function getId()
    {
        return $this->id;
    }

    /**
     * Get name of the plugin
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Get version number of the plugin
     * @return string
     */
    function getVersion()
    {
        return $this->version;
    }
}