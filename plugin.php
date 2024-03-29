<?php

/**
 * Class Entity
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 * Main Entity class
 * @since 1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Entity
{
    /**
     * Instance
     *
     * @since 1
     * @access private
     * @static
     *
     * @var Entity The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1
     * @access public
     *
     * @return Entity An instance of the class.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     *  Plugin class constructor
     *
     * Register plugin action hooks and filters
     *
     * @since 1.2.0
     * @access public
     */
    public function __construct()
    {
        define('PATH', __DIR__);
        register_activation_hook(__FILE__, array($this, 'plugin_activation'));
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));
        add_action('admin_enqueue_scripts', array($this, 'admin_include_script'), 11);
        $this->load_files();
    }

    public function admin_include_script()
    {
        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        // Enqueue scripts and styles
        // JS
        wp_enqueue_script('upload-media', plugin_dir_url(__FILE__) . '/assets/js/uploadMedia.js', null, false, true);
        wp_enqueue_script('upload-multi-media', plugin_dir_url(__FILE__) . '/assets/js/uploadMultiMedia.js', null, false, true);
        //CSS
        wp_enqueue_style('admin-icon', plugin_dir_url(__FILE__) . '/assets/pictofont/style.css', false, '1.0.0');
        wp_enqueue_style('admin-form', plugin_dir_url(__FILE__) . '/assets/css/admin-form.css', false, '1.0.0');


    }

    public function plugin_activation()
    {

        //Add code

    }

    public function plugin_deactivation()
    {
        //Add code
    }

    private function load_files()
    {
        $roots_includes = array(
            'entity',
            'src',
        );
        $pluginPath = __DIR__;

        if (is_dir($pluginPath)) {
            foreach ($roots_includes as $file) {
                $filepath = $pluginPath . '/' . $file . '/init.php';
                if (file_exists($filepath)) {
                    require_once $filepath;
                } else {
                    trigger_error("Error locating `$filepath` for inclusion!", E_USER_ERROR);
                }
            }
        }
    }

}

// Instantiate Plugin Class
Entity::instance();
