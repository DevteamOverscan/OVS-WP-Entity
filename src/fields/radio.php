<?php
/**
 * Custom field
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

use Ovs\ClassField\Field;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('Field_radio')) {
   class Field_radio extends Field
    {
        protected $options = [];
        /**
         * Get the value of options
         */
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * Set the value of options
         *
         * @return  self
         */
        public function setOptions($options)
        {
            $this->options = $options;

            return $this;
        }
        public function __construct($field, $value = false)
        {
            parent::__construct($field, $value);
            $this->setOptions(array_key_exists('options', $field) ? $field['options'] : false);

            // Valeur par défaut
            if (empty($this->getValue()) && isset($field['default'])) {
                $this->setValue($field['default']);
            }
        }

        public function render()
        {
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            $options = !empty($this->getOptions()) ? $this->getOptions() : false;

            if (!empty($this->getId())) {
                echo '<div class="form-row row-radio">';
                if (!empty($this->getLabel())) {
                    echo '<label>' . esc_html($this->getLabel()) . '</label>';
                }

                echo '<div class="choice-list">';
                if ($options) {
                    $i = 1;
                    foreach ($options as $value => $label) {
                        // Si l’array est numéroté (cas ['oui', 'non'])
                        if (is_int($value)) {
                            $value = $label;
                        }

                        $input_id = $this->getId() . '_' . $i;

                        echo '<div class="choice">';
                        echo '<input type="radio" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" ' . checked($this->getValue(), $value, false) . '>';
                        echo '<label for="' . esc_attr($input_id) . '">' . esc_html($label) . '</label>';
                        echo '</div>';

                        $i++;
                    }
                }
                echo '</div>';

                if (!empty($this->getSub_desc())) {
                    echo '<p>' . esc_html($this->getSub_desc()) . '</p>';
                }

                echo '</div>';
            } else {
                echo 'Erreur : l\'identifiant du champ est obligatoire.';
            }
        }
    }
}
