<?php
/**
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

// --------------------------------------- //
// --       Inclusion des fichiers      -- //
// --------------------------------------- //

$roots_includes = array(
        //----------Fields--------------
        'text',
        'date',
        'hour',
        'select',
        'checkbox',
        'radio',
        'upload',
        'upload-multi',
        'docutheque',
        'gallery-img',
        'color',
        );

if (is_dir($pluginPath)) {
    // plugin directory found!
    foreach ($roots_includes as $file) {
        $filepath = __DIR__ .'/'. $file . '.php';
        if (!file_exists($filepath)) {
            trigger_error("Error locating `$filepath` for inclusion!", E_USER_ERROR);
        }
        require_once $filepath;
    }
    unset($file, $filepath);
}
