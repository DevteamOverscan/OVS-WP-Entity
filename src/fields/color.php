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
if(!class_exists('Field_color')) {
    class Field_color extends Field
    {
        public function render()
        {
            $required = $this->getRequired() ? 'required' : '';
            $placeholder = !empty($this->getPlaceholder()) ? 'placeholder="' . $this->getPlaceholder() . '"' : '';
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            if(!empty($this->getId())) {
                echo '<div class="form-row row-color">';
                if(!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_attr($this->getLabel()) . '</label>';
                }
                echo '<input type="color" id="' . esc_attr($this->getId()) . '" name="' . esc_attr($name) . '" ' . esc_attr($required) . esc_attr($placeholder) . ' value="' . esc_attr($this->getValue()) . '">';
                if(!empty($this->getSub_desc())) {
                    echo '<p>' . esc_attr($this->getSub_desc()) . '</p>';
                }
                echo '</div>';
            } else {
                echo 'Erreur, l\'identifiant du champ est obligatoire. Vérifiez qu\'il ne soit pas vide.';
            }
        }
        public function columnContent()
        {
            $content = '<div class="field-color" style="background-color:' . $this->getValue() . '"></div>';
            return $content;
        }
    }
}
