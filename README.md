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

### Désactiver le préfixe du slug

Par défaut, le slug de la taxonomie est préfixé par le post type qui la déclare (`projet_categorie`). Pour une taxonomie partagée, ce préfixe n'a souvent plus de sens. L'option `'prefix' => false` utilise la clé telle quelle comme slug :

```php
'taxonomies' => [
    'equipment_type' => [
        'name' => 'Types d\'équipement',
        'prefix' => false,              // slug : "equipment_type" au lieu de "equipment_equipment_type"
        'post_types' => ['actu'],
    ],
],
```

Le template d'archive suit le slug : `templates/taxonomy-equipment_type.php`.

> ⚠️ Changer le slug d'une taxonomie existante détache ses termes en base : ils restent enregistrés sous l'ancien slug. Il faut les migrer (voir ci-dessous).

```sql
UPDATE wp_term_taxonomy SET taxonomy = 'equipment_type' WHERE taxonomy = 'equipment_equipment_type';
```

## Champ relation

Lie un contenu à d'autres contenus (un ou plusieurs post types), par exemple les moyens mobilisés par une prestation.

```php
new MetaBox('offer', 'offer_equipment', 'Moyens mobilisés', 'normal', 'default', [
    [
        'id'        => 'related_equipment',
        'type'      => 'relation',
        'label'     => 'Moyens mobilisés',
        'post_type' => 'equipment',   // string ou tableau
        'multiple'  => true,          // false : liste déroulante, un seul contenu
        'column'    => true,          // affiche les titres dans la liste de l'admin
    ],
]);
```

Les IDs sont enregistrés en tableau de chaînes. Côté front :

```php
// Moyens liés à la prestation, dans l'ordre enregistré
$equipments = Field_relation::getPosts(get_the_ID(), 'related_equipment', 'equipment');

// Requête inverse : prestations qui utilisent ce moyen
$offers = Field_relation::getReferencing(get_the_ID(), 'related_equipment', 'offer');
```
