<?php

namespace awis_wc_pf\inc;

/**
 * Object of child class based on this class must be in plugin root
 */

abstract class Plugin_Loader
{

    /** PROTECTED   */
    static protected $plugin_dir;
    static protected $plugin_url;
    static protected $plugin;
    static protected $plugin_public;
    static protected $plugin_admin;

    static protected $options = array();

    /**
     * Constructor
     * @param $plugin_dir
     * @param $plugin_url
     */
    function __construct($plugin_dir, $plugin_url)
    {
        self::$plugin_dir = $plugin_dir;
        self::$plugin_url = $plugin_url;

        //init auto_loader
        spl_autoload_register(array($this, 'auto_loader'), false);
    }

    /**
     * Get plugin instance
     * @return mixed
     */
    public static function getPlugin()
    {
        return self::$plugin;
    }

    /**
     * Get plugin public instance
     * @return mixed
     */
    public static function getPluginPublic()
    {
        return self::$plugin_public;
    }

    /**
     * Get plugin public admin
     * @return mixed
     */
    public static function getPluginAdmin()
    {
        return self::$plugin_admin;
    }

    /**
     * @return mixed
     */
    public static function getPluginDir()
    {
        return self::$plugin_dir;
    }

    /**
     * @return mixed
     */
    public static function getPluginUrl()
    {
        return self::$plugin_url;
    }

    /**
     * @return array
     * @param $key
     */
    public static function getOption($key)
    {
        return self::$options[$key];
    }

    /**
     * @param array $option
     * @param $key
     */
    public static function addOption($key, $option)
    {
        self::$options[$key] = $option;
    }

    /**
     * Resources auto_loader
     * @param $class_name
     */
    private function auto_loader($class_name)
    {
        // project-specific namespace prefix
        $prefix = 'awis_wc_pf\\';

        // base directory for the namespace prefix
        $base_dir = self::$plugin_dir;

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class_name, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class_name, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }

    /**
     * On plugin activation
     */
    abstract public function activation();

    /**
     * On plugin deactivation
     */
    abstract public function deactivation();
}