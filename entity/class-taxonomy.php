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

        public function __construct($taxonomy, $settings = [], $postId)
        {
            $defaults = [
                'name' => $taxonomy,
                'parent' => true,
                'public' => true,
                'fields' => []
            ];

            // Fusionne les options par défaut avec celles passées
            $settings = array_merge($defaults, $settings);

            $this->setId($taxonomy);
            $this->setName($settings['name']);
            $this->setPostId($postId);
            $this->setParent($settings['parent']);
            $this->setPublic($settings['public']);
            $this->setFields($settings['fields']);

            $this->addTaxonomy();

            add_filter('taxonomy_template', [$this, 'archiveTemplate']);
        }

        public function addTaxonomy()
        {
            register_taxonomy(
                $this->getPostId() . '_' . $this->getId(),
                $this->getPostId(),
                array(
                    'label' => esc_html__($this->getName(), 'ovs'),
                    'hierarchical' => $this->getParent(),
                    'public' => $this->getPublic(),
                    'show_ui' => true
                )
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
