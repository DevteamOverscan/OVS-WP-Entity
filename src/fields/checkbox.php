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
if(!class_exists('Field_checkbox')) {
    class Field_checkbox extends Field
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
            if($this->getValue() == false) {
                $this->setValue(array());
            }
        }
        public function render()
        {
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            $options = !empty($this->getOptions()) ? $this->getOptions() : false;
            if(!empty($this->getId())) {
                echo '<div class="form-row row-checkbox">';
                if(!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_attr($this->getLabel()) . '</label>';
                }
                echo '<div class="choice-list">';
                if($options) {
                    $i = 1;
                    foreach($options as $opt) {
                        $v = str_replace(' ', '_', $opt);
                        $v = $i . '_' . $this->getId() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $v);
                        $checked = is_array($this->getValue()) && in_array($v, $this->getValue()) ? 'checked' : '';

                        echo '<div class="choice">';
                        echo '<input type="checkbox" id="' . $v . '" name="' . $name . '[]" value="' . $v . '" ' . $checked . '>';
                        echo '<label for="' . $v . '">' . $opt . '</label>';
                        echo '</div>';
                        $i++;
                    }
                }
                echo '</div>';
                if(!empty($this->getSub_desc())) {
                    echo '<p>' . esc_attr($this->getSub_desc()) . '</p>';
                }
                echo '</div>';
            } else {
                echo 'Erreur, l\'identifiant du champ est obligatoire. Vérifiez qu\'il ne soit pas vide.';
            }
        }
    }
}
