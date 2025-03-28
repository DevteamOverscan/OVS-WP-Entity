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
        protected $name = ''; // Nom de la taxonomy afficher dans l'admin
        protected $parent = true; // Active le système de hiérarchie de la taxonomy
        protected $postId = ''; // l'id du postType lié à la taxonomy
        protected $fields = [];
        /**
         * Get the value of id
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set the value of id
         *
         * @return  self
         */
        public function setId($id)
        {
            $this->id = $id;

            return $this;
        }

        /**
         * Get the value of name
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set the value of name
         *
         * @return  self
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get the value of parent
         */
        public function getParent()
        {
            return $this->parent;
        }

        /**
         * Set the value of parent
         *
         * @return  self
         */
        public function setParent($parent)
        {
            $this->parent = $parent;

            return $this;
        }

        /**
         * Get the value of postId
         */
        public function getPostId()
        {
            return $this->postId;
        }

        /**
         * Set the value of postId
         *
         * @return  self
         */
        public function setPostId($postId)
        {
            $this->postId = $postId;

            return $this;
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

        public function __construct($taxonomy, $settings, $postId)
        {
            $this->setId($taxonomy);
            $this->setName(array_key_exists('name', $settings) ? $settings['name'] : $taxonomy);
            $this->setPostId($postId);
            $this->setParent(array_key_exists('parent', $settings) ? $settings['parent'] : true);
            $this->setFields(array_key_exists('fields', $settings) ? $settings['fields'] : []);
            $this->addTaxonomy();

            add_filter('taxonomy_template',  [$this, 'archiveTemplate']);

        }
        public function addTaxonomy()
        {

            register_taxonomy($this->getPostId() . '_' . $this->getId(), $this->getPostId(), array(
                'label' =>  esc_html__($this->getName(), 'ovs'),
                'hierarchical' => $this->getParent(),
            ));

            if(!empty($this->getFields())) {
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
        // Template Pour l'archive de la taxonomie

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
