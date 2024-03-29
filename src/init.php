<?php
/**
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// --------------------------------------- //
// --       Inclusion des fichiers      -- //
// --------------------------------------- //

$roots_includes = array(
        //----------Base--------------
        '/fields/init',
        );

$pluginPath = PATH;

if (is_dir($pluginPath)) {
    // plugin directory found!
    foreach ($roots_includes as $file) {
        $filepath = $pluginPath . '/src' . $file . '.php';
        if (!file_exists($filepath)) {
            trigger_error("Error locating `$filepath` for inclusion!", E_USER_ERROR);
        }
        require_once $filepath;
    }
    unset($file, $filepath);
}
