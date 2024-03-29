<?php

namespace Ovs\ClassMetaTaxonomy;

/**
 * Custom metabox
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Meta_Taxonomy')) {
    class Meta_Taxonomy
    {
        //Variables
        protected $taxonomy; // Id de la taxonomy liée
        protected $fields = []; // Champs à ajouter

        /**
         * Get the value of taxonomy
         */
        public function getTaxonomy()
        {
            return $this->taxonomy;
        }

        /**
         * Set the value of taxonomy
         *
         * @return  self
         */
        public function setTaxonomy($taxonomy)
        {
            $this->taxonomy = $taxonomy;

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
        /**
         * Constructor.
         * @param string $meta
         */
        public function __construct($taxonomy, $fields)
        {
            $this->setTaxonomy($taxonomy);
            $this->setFields($fields);
            $this->init();
        }
        public function init()
        {
            if (is_admin()) {
                add_action($this->getTaxonomy() . '_add_form_fields', array($this, 'render_term_fields'));
                add_action($this->getTaxonomy() . '_edit_form_fields', array($this, 'render_term_fields'));
                add_action('created_' . $this->getTaxonomy(), array($this, 'save_term_fields'), 10, 2);
                add_action('edited_' . $this->getTaxonomy(), array($this, 'save_term_fields'), 10, 2);

                // Ajoute/Modifie les colonnes a afficher dans la liste d'une taxonomie côté BO
                add_filter('manage_edit-' . $this->getTaxonomy() . '_columns', array($this, 'addColumns'));
                add_action('manage_' . $this->getTaxonomy() . '_custom_column', array($this, 'columnsContent'), 10, 3);

            }
        }
        /**
         * Render input upload add taxonomy
         */
        public function render_term_fields($term)
        {
            echo '<input type="hidden" name="meta-term-nonce" value="' . wp_create_nonce('meta-term-nonce') . '" />';
            echo '<div>';

            if($this->getFields() !== false && !empty($this->getFields())) {
                foreach($this->getFields() as $field) {
                    if(array_key_exists('type', $field)) {
                        $value = is_object($term) ? get_term_meta($term->term_id, $field['id'], true) : '';
                        $fieldClass = 'Field_' . strtolower($field['type']);
                        $f = new $fieldClass($field, $value);
                        echo $f->render();
                    } else {
                        echo '<p>Error, il faut indique le type de champs que vous voulez charger.</p>';
                    }
                }
            }

            echo '</div>';

        }

        public function save_term_fields($term_id)
        {

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Check if nonce is set
            if (!isset($_POST['meta-term-nonce'])) {
                return;
            }

            // Verify nonce
            if (!wp_verify_nonce($_POST['meta-term-nonce'], 'meta-term-nonce')) {
                return;
            }

            // Check if the current user has permission to edit the term
            $taxonomy = get_taxonomy($term_id)->taxonomy;
            if (!current_user_can($taxonomy->cap->edit_terms)) {
                return $term_id;
            }

            if ($this->getFields() !== false && !empty($this->getFields())) {
                foreach ($this->getFields() as $field) {

                    if (empty($field['id'])) {
                        continue;
                    }

                    if (isset($_POST[$field['id']])) {
                        $new = $_POST[$field['id']];
                    } else {
                        continue;
                    }
                    $old = get_term_meta($term_id, $field['id'], true);

                    if (isset($new) && ($new != $old)) {
                        update_term_meta($term_id, $field['id'], $new);
                    } elseif (('' == $new) && $old) {
                        delete_term_meta($term_id, $field['id']);
                    }
                }
            }
        }

        // Ajoute l'entête des nouvelles colonnes
        public function addColumns($columns)
        {
            $newColumns = [];
            foreach($this->getFields() as $field) {

                $fieldClass = 'Field_' . strtolower($field['type']);
                $f = new $fieldClass($field);
                if($f->getColumn()) {
                    $newColumns[$f->getID()] = $f->getLabel();
                }
            }

            return $columns = array_merge($columns, $newColumns);

        }

        // Ajoute le contenu des nouvelles colonnes
        public function columnsContent($content, $column_key, $item_id)
        {

            foreach($this->getFields() as $field) {
                $fieldClass = 'Field_' . strtolower($field['type']);
                $f = new $fieldClass($field);
                if($f->getColumn()) {
                    $value = get_term_meta($item_id, $column_key, true);
                    switch ($column_key) {
                        case $column_key:
                            $f->setValue($value);
                            $content =  $f->columnContent();
                            break;
                        default:
                            break;
                    }
                    return $content;

                }
            }
            return $content;
        }
    }
}
