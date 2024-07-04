# OVS WP Entity
## Installation
Vous avez la possibiliter d'installer le plugin : 
 - soit comme un plugin classic (dans le dossier wp-content/plugins)
 - soit comme un plugin obligatiore (dans le dossier wp-content/mu-plugins)

Si vous choississez la 2eme option, vous devez déclarer le plugin afin de l'activer : 
```php
<?php
/**
 * Plugin Name: Ovs
 * Description: Plugin prsonnalisé d'Overscan pour Wordpress
 * Plugin URI:  https://www.overscan.com/
 * Version:     1
 * Author:      Clément Vacheron
 * Author URI:  https://www.overscan.com/
 * Text Domain: ovs
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/quick-guide-gplv3.html
 */

/**
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//Package d'installation
require WPMU_PLUGIN_DIR . '/OVS-WP-Entity/ovs.php';

if(get_option('custom_plugins') !== false) {
    foreach (get_option('custom_plugins') as $plugin) {
        if(file_exists(WPMU_PLUGIN_DIR . '/'. $plugin.'/init.php')) {
            require WPMU_PLUGIN_DIR . '/'. $plugin.'/init.php';
        }
    }
}
````
