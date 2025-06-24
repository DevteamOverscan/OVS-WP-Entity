<?php
/**
 * Custom post types
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

use Ovs\ClassMetabox\MetaBox;
use Ovs\ClassTaxonomy\Taxonomy;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Post_Type')) {
    class Post_Type
    {
        /**
         * name = Nom du Post type / Entité
         * id = Identifiant du post type sans espace accent ou caractère spéciaux
         * icon = Icon présent à coté du nom dans le menu du BO
         */

        protected $id; // Identifiant du postType
        protected $name = ''; // Nom du postType afficher dans l'admin
        protected $isFeminin = false; // Masculin par défaut
        protected $icon = 'dashicons-admin-page'; // Icon du postType dans le menu de l'admin
        protected $rewriteSlug = '';
        protected $taxonomies = array(); // Les Taxonomies liées au postType
        protected $fields = [];
        protected $publicly_queryable = true; 
        protected $has_archive = true; 

        protected function setName($value)
        {
            $this->name = $value;
            return $this->name;
        }
        public function getName()
        {
            return $this->name;
        }
        protected function setId($value)
        {
            $this->id = strtolower(str_replace(' ', '_', $value));
            return $this->id;
        }
        public function getId()
        {
            return $this->id;
        }
        public function isFeminin(): bool
        {
            return $this->isFeminin;
        }

        public function setIsFeminin(bool $isFeminin): self
        {
            $this->isFeminin = $isFeminin;
            return $this;
        }
        protected function setIcon($value)
        {
            $this->icon = $value;
            return $this->icon;
        }
        public function getIcon()
        {
            return $this->icon;
        }
        protected function setRewriteSlug($value)
        {
            $this->rewriteSlug = $value;
            return $this->rewriteSlug;
        }
        public function getRewriteSlug()
        {
            return $this->rewriteSlug;
        }
        protected function setPubliclyQueryable($value)
        {
            $this->publicly_queryable = $value;
            return $this->publicly_queryable;
        }
        public function getPubliclyQueryable()
        {
            return $this->publicly_queryable;
        }

        protected function setHasArchive($value)
        {
            $this->has_archive = $value;
            return $this->has_archive;
        }
        public function getHasArchive()
        {
            return $this->has_archive;
        }

        protected function setTaxonomies($value = array())
        {
            $this->taxonomies = $value;
            return $this->taxonomies;
        }
        public function getTaxonomies()
        {
            return $this->taxonomies;
        }
        /**
         * Get the value of fields
         */
        public function getFields()
        {
            return $this->fields;
        }

        /**
         * Set the value of fields
         *
         * @return  self
         */
        public function setFields($fields)
        {
            $this->fields = $fields;

            return $this;
        }
        /**
         * Post_Type constructor
         */

        public function __construct(array $args = [])
        {

            $defaults = [
                'id' => '',
                'name' => '',
                'isFeminin' => false,
                'icon' => 'dashicons-admin-page',
                'publicly_queryable' => true,
                'has_archive' => true,
                'taxonomies' => [],
                'fields' => []
            ];
        
            // Fusionne les valeurs par défaut avec celles fournies
            $args = array_merge($defaults, $args);

            // Initialise les variable
            $this->setId($args['id']);
            $this->setName($args['name']);
            $this->setIsFeminin($args['isFeminin']);
            $this->setIcon(empty($args['icon']) ? $this->getIcon() : $args['icon']);
            $this->setRewriteSlug(!empty($args['rewriteSlug']) ? $args['rewriteSlug'] : "");
            $this->setPubliclyQueryable($args['publicly_queryable']);
            $this->setHasArchive($args['has_archive']);
            $this->setTaxonomies($args['taxonomies']);

            if($args['fields']) {
                $this->setFields($args['fields']);
            }

            // se déclenche après la fin du chargement de WordPress mais avant l'envoi des en-têtes
            add_action('init', array($this, 'register'));

            if(is_admin()) {
                new MetaBox($this->getId(), $this->getId() . '_settings', $this->getName() . ' Options', 'normal', 'high', $this->getFields(), array('options'));
            }
            // Si un template est définis pour ce post dans le dossier templates il sera affiché. Sinon par défaut se sera le template de Wordpress ou du thème
            add_filter('single_template', array($this,'singleTemplate'));

        }

        /**
         * Enregistrer un nouveau type de publication et la taxonomie associée
         */

        public function register()
        {
            $feminin = $this->isFeminin();
            $name = $this->getName();
            $name_lower = mb_strtolower($name); // Pour utilisation dans les libellés
        
            $labels = [
                'name' => esc_html__($name, 'ovs'),
                'singular_name' => esc_html__($name, 'ovs'),
                'add_new' => esc_html__('Ajouter', 'ovs'),
                'add_new_item' => $feminin
                    ? esc_html__('Ajouter une ' . $name_lower, 'ovs')
                    : esc_html__('Ajouter un ' . $name_lower, 'ovs'),
                'edit_item' => $feminin
                    ? esc_html__('Modifier la ' . $name_lower, 'ovs')
                    : esc_html__('Modifier le ' . $name_lower, 'ovs'),
                'new_item' => $feminin
                    ? esc_html__('Nouvelle ' . $name_lower, 'ovs')
                    : esc_html__('Nouveau ' . $name_lower, 'ovs'),
                'view_item' => $feminin
                    ? esc_html__('Voir la ' . $name_lower, 'ovs')
                    : esc_html__('Voir le ' . $name_lower, 'ovs'),
                'search_items' => $feminin
                    ? esc_html__('Rechercher des ' . $name_lower, 'ovs')
                    : esc_html__('Rechercher des ' . $name_lower, 'ovs'),
                'not_found' => $feminin
                    ? esc_html__('Aucune ' . $name_lower . ' trouvée', 'ovs')
                    : esc_html__('Aucun ' . $name_lower . ' trouvé', 'ovs'),
                'not_found_in_trash' => $feminin
                    ? esc_html__('Aucune ' . $name_lower . ' dans la corbeille', 'ovs')
                    : esc_html__('Aucun ' . $name_lower . ' dans la corbeille', 'ovs'),
                'menu_name' => esc_html__($name, 'ovs'),
                'all_items' => $feminin
                    ? esc_html__('Toutes les ' . $name_lower, 'ovs')
                    : esc_html__('Tous les ' . $name_lower, 'ovs'),
            ];
        
            $args = array(
                'labels' => $labels,
                'menu_icon' => $this->getIcon(),
                'publicly_queryable' => $this->getPubliclyQueryable(),
                'show_ui' => true,
                'has_archive' => $this->getHasArchive(),
                'show_in_rest' => true,
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
            );
            if (!empty($this->getRewriteSlug())) {
                $args['rewrite'] = array(
                    'slug' => $this->getRewriteSlug(),
                );
            }

            register_post_type($this->getId(), $args);

            if(count($this->getTaxonomies()) > 0) {
                $this->addTaxonomies();
            }
        }
        protected function addTaxonomies()
        {
            if(count($this->getTaxonomies()) > 0) {
                foreach ($this->getTaxonomies() as $key => $taxo) {
                    new Taxonomy($key, $this->getId(), $taxo);
                }
            }
        }

        public function AddOneTaxonomy($value = array())
        {

            $taxonomies = $this->getTaxonomies();
            // Ajoute la nouvelle taxonomie à la fin du tableau
            $this-> setTaxonomies($taxonomies += $value); // Met à jour la propriété taxonomies
        }



        public function singleTemplate($single)
        {
            
            $template =  get_stylesheet_directory() . '/templates/posts/' . $this->getId() . '.php';

            // Vérifiez si le fichier existe
            if (file_exists($template)) {
                // Spécifiez le chemin vers le fichier modèle dans le sous-dossier "templates"
                return $template;

            }

            return $single;

        }

        public function removePostType()
        {
            add_action('init', function () {
                unregister_post_type($this->getId());
            });
        }

    }
}
