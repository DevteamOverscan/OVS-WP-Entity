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
                $hasValue = !empty($this->getValue());

                echo '<div class="form-row row-upload">';
                if(!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_attr($this->getLabel()) . '</label>';
                }

                echo '<div class="row-upload__field">';

                    echo '<div class="row-upload__controls">';
                        echo '<input type="hidden" class="row-upload__input" id="' . esc_attr($this->getId()) . '" name="' . esc_attr($name) . '" value="' . esc_attr($this->getValue()) . '" ' . $placeholder;
                        if ($this->getRequired()) {
                            echo ' required';
                        }
                        echo '>';
                        echo '<button type="button" class="button button-primary js-media-upload" data-field="' . esc_attr($this->getId()) . '">Upload</button>';
                    echo '</div>';

                    echo '<div class="row-upload__preview-wrap' . ($hasValue ? ' is-visible' : '') . '" id="' . esc_attr($this->getId()) . '-preview-wrap">';
                        echo '<span class="row-upload__loader" id="' . esc_attr($this->getId()) . '-loader"></span>';
                        echo '<img class="row-upload__preview" id="' . esc_attr($this->getId()) . '-preview" src="' . esc_attr($this->getValue()) . '" alt="" />';
                    echo '</div>';

                echo '</div>';

                if (!empty($this->getSub_desc())) {
                    echo '<p class="row-upload__desc">' . esc_html($this->getSub_desc()) . '</p>';
                }
                echo '</div>';
            } else {
                echo 'Erreur, l\'identifiant du champ est obligatoire. Vérifiez qu\'il ne soit pas vide.';
            }
        }
    }
}