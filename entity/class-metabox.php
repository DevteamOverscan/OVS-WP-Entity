<?php

namespace Ovs\ClassMetabox;

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
if (!class_exists('MetaBox')) {

    class MetaBox
    {
        private $post_type; // Post Type auquel lié la metabox
        private $meta_box_id; // Identifiant de la metabox
        private $meta_box_title; // Nom de la metabox
        private $meta_box_context; // Endroit ou afficher la metabox, au centre ou dans la sidebar de droite (normal,side)
        private $meta_box_priority; // Priorité d'affichage / Position (default, nomal, high)
        private $meta_box_class = []; // Class CSS de la metabox
        protected $fields = [];
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
         * Get the value of meta_box_class
         */
        public function getMetaBoxClass()
        {
            return $this->meta_box_class;
        }

        /**
         * Set the value of meta_box_class
         *
         * @return  self
         */
        public function setMetaBoxClass($class)
        {
            $this->meta_box_class = $class;

            return $this;
        }
        public function __construct($post_type, $meta_box_id, $meta_box_title, $meta_box_context = 'normal', $meta_box_priority = 'default', $fields = false, $class = [])
        {
            $this->post_type = $post_type;
            $this->meta_box_id = $meta_box_id;
            $this->meta_box_title = $meta_box_title;
            $this->meta_box_context = $meta_box_context;
            $this->meta_box_priority = $meta_box_priority;
            $this->setFields($fields);
            $this->setMetaBoxClass($class);

            // Add the meta box
            add_action('add_meta_boxes', array($this, 'addMetaBox'));

            // Save the meta box data
            add_action('save_post', array($this, 'saveMetaBox'));

            // Ajoute/Modifie les colonnes a afficher dans la liste d'un post-type côté BO
            add_filter('manage_' . $this->post_type . '_posts_columns', array($this, 'addColumns'));
            add_action('manage_' . $this->post_type . '_posts_custom_column', array($this, 'columnsContent'), 10, 2);

            $this->registerRestMeta();
        }

        // Callback to add the meta box
        public function addMetaBox()
        {
            add_meta_box(
                $this->meta_box_id,
                $this->meta_box_title,
                array($this, 'renderMetaBox'),
                $this->post_type,
                $this->meta_box_context,
                $this->meta_box_priority
            );
        }

        // Callback to render the meta box content
        public function renderMetaBox($post)
        {
            global $post;
            // Implement your meta box content here
            echo '<input type="hidden" name="metabox-nonce" value="' . wp_create_nonce('metabox-nonce') . '" />';
            echo '<div class="' . implode(',', $this->getMetaBoxClass()) . '">';
            if($this->getFields() !== false && !empty($this->getFields())) {
                foreach($this->getFields() as $field) {
                    if(array_key_exists('type', $field)) {
                        $value = get_post_meta($post->ID, $field['id'], true);
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

        // Callback to save the meta box data
        public function saveMetaBox($post_id)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Check if nonce is set
            if (!isset($_POST['metabox-nonce'])) {
                return;
            }

            // Verify nonce
            if (!wp_verify_nonce($_POST['metabox-nonce'], 'metabox-nonce')) {
                return;
            }

            // check permissions

            if (isset($_POST['post_type']) && ('page' == $_POST['post_type'])) {
                if (! current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } elseif (! current_user_can('edit_post', $post_id)) {
                return $post_id;
            }

            // Check if the current user has permission to edit the post
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }

            if($this->getFields() !== false && !empty($this->getFields())) {
                foreach ($this->getFields() as $field) {

                    if (empty($field['id'])) {
                        continue;
                    }

                    if (isset($_POST[$field['id']])) {
                        $new = $_POST[$field['id']];
                    } else {
                        continue;
                    }
                    $old = get_post_meta($post_id, $field['id'], true);

                    if (isset($new) && ($new != $old)) {
                        update_post_meta($post_id, $field['id'], $new);
                    } elseif (('' == $new) && $old) {
                        delete_post_meta($post_id, $field['id'], $old);
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
        public function columnsContent($column_key, $item_id)
        {

            foreach($this->getFields() as $field) {
                $fieldClass = 'Field_' . strtolower($field['type']);
                $f = new $fieldClass($field);
                if($f->getColumn()) {
                    $value = get_post_meta($item_id, $column_key, true);
                    switch ($column_key) {
                        case $column_key:
                            $f->setValue($value);
                            echo  $f->columnContent();
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        public function registerRestMeta() {
            add_action('rest_api_init', function() {
                if ($this->getFields()) {
                    foreach ($this->getFields() as $field) {
                        if (!empty($field['id'])) {
                            // Détection du type de champ pour REST
                            $type = 'string'; // par défaut
                            if (!empty($field['rest_type'])) {
                                $type = $field['rest_type'];
                            } elseif (!empty($field['type'])) {
                                // Auto-détection basique
                                switch ($field['type']) {
                                    case 'checkbox':
                                    case 'boolean':
                                        $type = 'boolean';
                                        break;
                                    case 'number':
                                        $type = 'number';
                                        break;
                                    case 'array':
                                        $type = 'array';
                                        break;
                                    case 'text':
                                    case 'textarea':
                                    default:
                                        $type = 'string';
                                        break;
                                }
                            }
            
                            register_post_meta($this->post_type, $field['id'], [
                                'show_in_rest' => true,
                                'single' => true,
                                'type' => $type,
                                'auth_callback' => '__return_true'
                            ]);
                        }
                    }
                }
            });
        }
        
    }
}
