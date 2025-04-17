<?php
/**
 * Custom textarea field
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

use Ovs\ClassField\Field;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Field_textarea')) {
    class Field_textarea extends Field
    {
        public function render()
        {
            $required = $this->getRequired() ? 'required' : '';
            $placeholder = !empty($this->getPlaceholder()) ? 'placeholder="' . $this->getPlaceholder() . '"' : '';
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();

            if (!empty($this->getId())) {
                echo '<div class="form-row row-textarea">';
                if (!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_html($this->getLabel()) . '</label>';
                }
                echo '<textarea id="' . esc_attr($this->getId()) . '" name="' . esc_attr($name) . '" ' . esc_attr($required) . ' ' . $placeholder . '>';
                echo esc_textarea($this->getValue());
                echo '</textarea>';
                if (!empty($this->getSub_desc())) {
                    echo '<p>' . esc_html($this->getSub_desc()) . '</p>';
                }
                echo '</div>';
            } else {
                echo 'Erreur, l\'identifiant du champ est obligatoire. Vérifiez qu\'il ne soit pas vide.';
            }
        }
    }
}
