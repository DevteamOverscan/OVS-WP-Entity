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
if(!class_exists('Field_upload')) {
    class Field_upload extends Field
    {
        public function render()
        {
            $placeholder = !empty($this->getPlaceholder()) ? 'placeholder="' . $this->getPlaceholder() . '"' : '';
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            if(!empty($this->getId())) {
                echo '<div class="form-row row-upload">';
                if(!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_attr($this->getLabel()) . '</label>';
                }
                // Output the input field for file upload
                echo '<input type="text" id="' . esc_attr($this->getId()) . '" name="' . esc_attr($name) . '" value="' . esc_attr($this->getValue()) . '"' . $placeholder;

                if ($this->getRequired()) {
                    echo ' required';
                }

                echo '>';

                // Add a button to open the media library
                echo '<button type="button" class="button button-primary js-media-upload" data-field="' . esc_attr($this->getId()) . '">Upload</button>';

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
