<?php

namespace Ovs\ClassTaxonomy;

use Ovs\ClassMetaTaxonomy\Meta_Taxonomy;

/**
 * Custom post types
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Taxonomy')) {
    class Taxonomy
    {
        protected $id = ''; // Identifiant de la taxonomy
        protected $name = ''; // Nom de la taxonomy affiché dans l'admin
        protected $isFeminin = false; // Masculin par défaut
        protected $parent = true; // Active le système de hiérarchie de la taxonomy
        protected $public = true; // Rend la taxonomie publique
        protected $postId = ''; // l'id du postType lié à la taxonomy
        protected $fields = [];


        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
            return $this;
        }

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
            return $this;
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

        public function getParent()
        {
            return $this->parent;
        }

        public function setParent($parent)
        {
            $this->parent = $parent;
            return $this;
        }

        public function getPublic()
        {
            return $this->public;
        }

        public function setPublic($public)
        {
            $this->public = $public;
            return $this;
        }

        public function getPostId()
        {
            return $this->postId;
        }

        public function setPostId($postId)
        {
            $this->postId = $postId;
            return $this;
        }

        public function getFields()
        {
            return $this->fields;
        }

        public function setFields($fields)
        {
            $this->fields = $fields;
            return $this;
        }

        public function __construct($taxonomy, $postId, $settings = [])
        {
            $defaults = [
                'name' => $taxonomy,
                'isFeminin' => false,
                'parent' => true,
                'public' => true,
                'fields' => [],
            ];

            // Fusionne les options par défaut avec celles passées
            $settings = array_merge($defaults, $settings);

            $this->setId($taxonomy);
            $this->setName($settings['name']);
            $this->setIsFeminin($settings['isFeminin']);
            $this->setPostId($postId);
            $this->setParent($settings['parent']);
            $this->setPublic($settings['public']);
            $this->setFields($settings['fields']);

            $this->addTaxonomy();

            add_filter('taxonomy_template', [$this, 'archiveTemplate']);
        }

        public function addTaxonomy()
        {
            $feminin = $this->isFeminin();
            $name_lower = strtolower($this->getName());

            $labels = [
                'name' => esc_html__($this->getName(), 'ovs'),
                'singular_name' => esc_html__($this->getName(), 'ovs'),
                'search_items' => esc_html__('Rechercher des ' . $name_lower, 'ovs'),
                'all_items' => $feminin 
                    ? esc_html__('Toutes les ' . $name_lower, 'ovs') 
                    : esc_html__('Tous les ' . $name_lower, 'ovs'),
                'parent_item' => $this->getParent() 
                    ? esc_html__($name_lower . ' parent', 'ovs') 
                    : null,
                'edit_item' => $feminin
                    ? esc_html__('Modifier la ' . $name_lower, 'ovs')
                    : esc_html__('Modifier le ' . $name_lower, 'ovs'),
                'update_item' => esc_html__('Mettre à jour', 'ovs'),
                'add_new_item' => $feminin
                    ? esc_html__('Ajouter une nouvelle ' . $name_lower, 'ovs')
                    : esc_html__('Ajouter un nouveau ' . $name_lower, 'ovs'),
                'new_item_name' => $feminin
                    ? esc_html__('Nom de la nouvelle ' . $name_lower, 'ovs')
                    : esc_html__('Nom du nouveau ' . $name_lower, 'ovs'),
                'menu_name' => esc_html__($this->getName(), 'ovs')
            ];
        
            $args = array(
                'labels'            => $labels,
                'hierarchical'     => $this->getParent(),
                'public'           => $this->getPublic(),
                'show_ui'          => true,
                'show_admin_column'=> true,
            );
        
            register_taxonomy(
                $this->getPostId() . '_' . $this->getId(),
                $this->getPostId(),
                $args
            );
        
            if (!empty($this->getFields())) {
                $taxoId = $this->getPostId() . '_' . $this->getId();
                $meta = new Meta_Taxonomy($taxoId, $this->getFields());
            }
        }
        
        public function editTaxonomy($id, $name, $parent = true, $fields = [])
        {
            $this->setId($id);
            $this->setName($name);
            $this->setParent($parent);
            $this->setFields($fields);
        }

        public function removeTaxonomy()
        {
            add_action('init', function () {
                unregister_taxonomy($this->getId());

                $terms = get_terms($this->getId(), array('hide_empty' => false));

                foreach ($terms as $term) {
                    wp_delete_term($term->term_id, $this->getId());
                }
            });
        }

        public function archiveTemplate($default_template)
        {
            $template = get_stylesheet_directory() . '/templates/taxonomy-' . $this->getPostId() . '_' . $this->getId() . '.php';

            if (file_exists($template)) {
                return $template;
            }

            return $default_template;
        }
    }
}
