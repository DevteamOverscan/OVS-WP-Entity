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
        protected $postTypes = []; // postTypes supplémentaires auxquels la taxonomy est rattachée
        protected $prefix = true; // Préfixe le slug de la taxonomy avec l'id du postType ({postType}_{id})
        protected $default_term = []; // terme crée automatiquement et qui sera assigné par défaut si on en choissis aucun
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

        // Slug réel de la taxonomy enregistrée dans WordPress : {postType}_{id}, ou {id} si le préfixe est désactivé
        public function getSlug()
        {
            return $this->getPrefix() ? $this->getPostId() . '_' . $this->getId() : $this->getId();
        }

        public function getPrefix(): bool
        {
            return $this->prefix;
        }

        public function setPrefix(bool $prefix): self
        {
            $this->prefix = $prefix;
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

        public function getPostTypes()
        {
            return $this->postTypes;
        }

        public function setPostTypes($postTypes)
        {
            $this->postTypes = (array) $postTypes;
            return $this;
        }

        public function getDefaultTerm()
        {
            return $this->default_term;
        }

        public function setDefaultTerm($default_term)
        {
            $this->default_term = $default_term;
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
                'default_term' => [],
                'post_types' => [],
                'prefix' => true,
                'fields' => [],
            ];

            // Fusionne les options par défaut avec celles passées
            $settings = array_merge($defaults, $settings);

            $this->setId($taxonomy);
            $this->setName($settings['name']);
            $this->setIsFeminin($settings['isFeminin']);
            $this->setPostId($postId);
            $this->setPostTypes($settings['post_types']);
            $this->setPrefix($settings['prefix']);
            $this->setParent($settings['parent']);
            $this->setPublic($settings['public']);
            $this->setDefaultTerm($settings['default_term']);
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
                'show_admin_column' => true,
                'show_in_quick_edit' => true,
                'show_in_rest' => true,
                'default_term' => $this->getDefaultTerm(),
            );

            register_taxonomy(
                $this->getSlug(),
                array_values(array_unique(array_merge([$this->getPostId()], $this->getPostTypes()))),
                $args
            );

            if (!empty($this->getFields())) {
                $meta = new Meta_Taxonomy($this->getSlug(), $this->getFields());
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
            $remove = function () {
                $slug = $this->getSlug();

                // Les termes doivent être supprimés avant de désenregistrer la taxonomy,
                // sinon get_terms() et wp_delete_term() renvoient une erreur "taxonomie invalide"
                $terms = get_terms(['taxonomy' => $slug, 'hide_empty' => false]);

                if (!is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        wp_delete_term($term->term_id, $slug);
                    }
                }

                unregister_taxonomy($slug);
            };

            // La taxonomy est enregistrée pendant 'init' : si on y est déjà, on supprime directement
            if (did_action('init')) {
                $remove();
            } else {
                add_action('init', $remove, 20);
            }
        }

        public function archiveTemplate($default_template)
        {
            $template = get_stylesheet_directory() . '/templates/taxonomy-' . $this->getSlug() . '.php';

            if (file_exists($template)) {
                return $template;
            }

            return $default_template;
        }
    }
}