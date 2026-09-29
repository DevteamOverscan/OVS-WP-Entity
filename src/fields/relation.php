<?php
/**
 * Custom field : relation vers d'autres contenus (post types)
 *
 * Options :
 *  - post_type : string|array, post type(s) proposés (défaut 'post')
 *  - multiple  : bool, sélection multiple (défaut true)
 *
 * Valeur enregistrée : tableau d'IDs (en chaînes) si multiple, sinon un ID.
 * Les IDs sont stockés en chaînes pour permettre la requête inverse (voir getReferencing).
 *
 * @package OVS
 * @link https://www.overscan.com
 */

use Ovs\ClassField\Field;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Field_relation')) {
    class Field_relation extends Field
    {
        protected $postTypes = ['post'];
        protected $multiple = true;

        public function getPostTypes()
        {
            return $this->postTypes;
        }

        public function setPostTypes($postTypes)
        {
            $this->postTypes = (array) $postTypes;
            return $this;
        }

        public function isMultiple(): bool
        {
            return $this->multiple;
        }

        public function setMultiple(bool $multiple): self
        {
            $this->multiple = $multiple;
            return $this;
        }

        public function __construct($field, $value = false)
        {
            parent::__construct($field, $value);
            $this->setPostTypes(array_key_exists('post_type', $field) ? $field['post_type'] : 'post');
            $this->setMultiple(array_key_exists('multiple', $field) ? (bool) $field['multiple'] : true);
        }

        // IDs sélectionnés, toujours sous forme de tableau de chaînes
        protected function getSelectedIds(): array
        {
            $value = $this->getValue();
            if (empty($value)) {
                return [];
            }
            return array_map('strval', (array) $value);
        }

        protected function getChoices(): array
        {
            global $post;

            return get_posts([
                'post_type' => $this->getPostTypes(),
                'post_status' => ['publish', 'future', 'draft', 'pending', 'private'],
                'numberposts' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
                'exclude' => is_object($post) ? [$post->ID] : [],
            ]);
        }

        protected function choiceLabel($p): string
        {
            $label = get_the_title($p);
            if (count($this->getPostTypes()) > 1) {
                $pto = get_post_type_object($p->post_type);
                $label .= ' — ' . ($pto ? $pto->labels->singular_name : $p->post_type);
            }
            if ($p->post_status !== 'publish') {
                $status = get_post_status_object($p->post_status);
                $label .= ' (' . ($status ? $status->label : $p->post_status) . ')';
            }
            return $label;
        }

        public function render()
        {
            if (empty($this->getId())) {
                echo 'Erreur, l\'identifiant du champ est obligatoire. Vérifiez qu\'il ne soit pas vide.';
                return;
            }

            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            $selected = $this->getSelectedIds();
            $choices = $this->getChoices();

            echo '<div class="form-row row-relation">';
            if (!empty($this->getLabel())) {
                echo '<label class="relation-title" for="' . esc_attr($this->getId()) . '_search">' . esc_html($this->getLabel()) . '</label>';
            }

            if (empty($choices)) {
                echo '<p class="relation-empty">' . esc_html__('Aucun contenu disponible.', 'ovs') . '</p>';
            } elseif ($this->isMultiple()) {
                // Contenus déjà liés en premier
                usort($choices, function ($a, $b) use ($selected) {
                    return (int) in_array((string) $b->ID, $selected, true) - (int) in_array((string) $a->ID, $selected, true);
                });

                // Valeur vide envoyée si rien n'est coché : permet de vider la relation à l'enregistrement
                echo '<input type="hidden" name="' . esc_attr($name) . '" value="">';
                echo '<input type="search" class="relation-search" id="' . esc_attr($this->getId()) . '_search" placeholder="' . esc_attr__('Rechercher…', 'ovs') . '">';
                echo '<ul class="relation-list">';
                foreach ($choices as $p) {
                    $inputId = $this->getId() . '_' . $p->ID;
                    $checked = in_array((string) $p->ID, $selected, true) ? 'checked' : '';
                    echo '<li class="relation-item">';
                    echo '<input type="checkbox" id="' . esc_attr($inputId) . '" name="' . esc_attr($name) . '[]" value="' . esc_attr($p->ID) . '" ' . $checked . '>';
                    echo '<label for="' . esc_attr($inputId) . '">' . esc_html($this->choiceLabel($p)) . '</label>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                $current = $selected[0] ?? '';
                echo '<select id="' . esc_attr($this->getId()) . '_search" name="' . esc_attr($name) . '">';
                echo '<option value="">' . esc_html(!empty($this->getPlaceholder()) ? $this->getPlaceholder() : '--') . '</option>';
                foreach ($choices as $p) {
                    echo '<option value="' . esc_attr($p->ID) . '" ' . selected($current, (string) $p->ID, false) . '>' . esc_html($this->choiceLabel($p)) . '</option>';
                }
                echo '</select>';
            }

            if (!empty($this->getSub_desc())) {
                echo '<p class="relation-desc">' . esc_html($this->getSub_desc()) . '</p>';
            }
            echo '</div>';
        }

        public function sanitize($value)
        {
            if ($this->isMultiple()) {
                $ids = array_filter(array_map('absint', (array) $value));
                $ids = array_values(array_unique(array_map('strval', $ids)));
                return empty($ids) ? '' : $ids;
            }
            $id = absint(is_array($value) ? reset($value) : $value);
            return $id ? (string) $id : '';
        }

        public function columnContent()
        {
            $titles = array_filter(array_map('get_the_title', $this->getSelectedIds()));
            return '<div>' . esc_html(implode(', ', $titles)) . '</div>';
        }

        /**
         * Contenus liés, dans l'ordre enregistré (publiés uniquement par défaut)
         *
         * Field_relation::getPosts(get_the_ID(), 'related_equipment');
         *
         * @return WP_Post[]
         */
        public static function getPosts($postId, $fieldId, $postType = 'any', array $args = []): array
        {
            $ids = array_filter(array_map('absint', (array) get_post_meta($postId, $fieldId, true)));
            if (empty($ids)) {
                return [];
            }
            return get_posts(array_merge([
                'post_type' => $postType,
                'post__in' => $ids,
                'orderby' => 'post__in',
                'numberposts' => -1,
            ], $args));
        }

        /**
         * Requête inverse : contenus qui référencent $targetId dans le champ $fieldId
         *
         * Field_relation::getReferencing(get_the_ID(), 'related_equipment', 'offer');
         *
         * @return WP_Post[]
         */
        public static function getReferencing($targetId, $fieldId, $postType = 'any', array $args = []): array
        {
            return get_posts(array_merge([
                'post_type' => $postType,
                'numberposts' => -1,
                'meta_query' => [[
                    'key' => $fieldId,
                    'value' => '"' . absint($targetId) . '"',
                    'compare' => 'LIKE',
                ]],
            ], $args));
        }
    }
}
