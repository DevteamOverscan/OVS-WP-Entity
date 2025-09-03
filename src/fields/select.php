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
if(!class_exists('Field_select')) {
    class Field_select extends Field
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
        }
        public function render()
        {
            $required = $this->getRequired() ? 'required' : '';
            $placeholder = !empty($this->getPlaceholder()) ? 'placeholder="' . $this->getPlaceholder() . '"' : '--';
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            $options = !empty($this->getOptions()) ? $this->getOptions() : false;
            if(!empty($this->getId())) {
                echo '<div class="form-row row-select">';
                if(!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_attr($this->getLabel()) . '</label>';
                }
                echo '<select id="' . esc_attr($this->getId()) . '" name="' . esc_attr($name) . '" ' . esc_attr($required) . '">';
                echo '<option value="">' . $placeholder . '</option>';
                if($options) {
                    foreach($options as $opt) {
                        $v = str_replace(' ', '_', $opt);
                        $v = preg_replace('/[^a-zA-Z0-9]/', '', $v);
                        echo '<option value="' . $v . '" ' . selected($this->getValue(), $v, false) . '>' . $opt . '</option>';
                    }
                }
                echo '</select>';
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
