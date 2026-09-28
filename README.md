# OVS-WP-Entity

## À effetuer si vous n'utilisez pas le boiler de Overscan

Dans le dossier "mu-plugins" créer un fichier _"ovs.php"_. Ajouter le code suivant à l'intérieur du fichier nouvellement créé.

```php
<?php
/**
 * Plugin Name: Ovs
 * Description: Utilitaire pour faciliter la création de custom Post type, Taxonomies et Metabox dans Wordpress
 * Plugin URI:  https://www.overscan.com/
 * Version:     1
 * Author:      Overscan
 * Author URI:  https://www.overscan.com/
 * Text Domain: ovs
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/quick-guide-gplv3.html
 */

/**
 *
 * @package OVS
 * @author Overscan
 * @link https://www.overscan.com
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//Package d'installation
require WPMU_PLUGIN_DIR . '/ovs-wp-entity/init.php';

if(get_option('custom_plugins') !== false) {
    foreach (get_option('custom_plugins') as $plugin) {
        if(file_exists(WPMU_PLUGIN_DIR . '/'. $plugin.'/init.php')) {
            require WPMU_PLUGIN_DIR . '/'. $plugin.'/init.php';
        }
    }
}
```

## Partager une taxonomie entre plusieurs post types

Une taxonomie est déclarée dans le post type principal. Son slug est préfixé par ce post type (`{postType}_{taxonomy}`). L'option `post_types` la rattache aussi à d'autres post types, qui partagent alors les mêmes termes :

```php
new Post_Type([
    'id' => 'projet',
    'name' => 'Projets',
    'taxonomies' => [
        'categorie' => [
            'name' => 'Catégories',
            'isFeminin' => true,
            'post_types' => ['actu', 'evenement'], // taxonomie "projet_categorie" partagée
        ],
    ],
]);
```

Ne redéclarez pas la taxonomie dans les autres post types : cela créerait des taxonomies distinctes (`actu_categorie`, ...).
