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
if(!class_exists('Field_gallery_img')) {
    class Field_gallery_img extends Field
    {
        public function render()
        {
            $name = !empty($this->getName()) ? $this->getName() : $this->getId();
            if (!empty($this->getId())) {
                echo '<div class="form-row row-upload-multi">';
                if (!empty($this->getLabel())) {
                    echo '<label for="' . esc_attr($this->getId()) . '">' . esc_attr($this->getLabel()) . '</label>';
                }
                echo '<div class="options">';

                // Output the input field for file upload
                echo '<input type="hidden" class="upload-input field-value" id="' . esc_attr($this->getId()) . '" name="' . esc_attr($name) . '" value="' . esc_attr($this->getValue()) . '" />';

                // Add a button to open the media library
                echo '<button class="button-upload-multi" data-field="' . esc_attr($this->getId()) . '" data-multiple="true" data-type="image">Upload Media</button>';

                echo '<button class="button-delete-all" title="Delete">' . esc_html__('Delete all', 'ovs') . '</button>';

                if (!empty($this->getSub_desc())) {
                    echo '<p>' . esc_html($this->getSub_desc()) . '</p>';
                }
                echo '</div>';
                echo '<ul class="gallery-container clearfix">';
                if (!empty($this->getValue())) {
                    $images = explode(',', $this->getValue());

                    foreach ($images as $img_id) {
                        $img_src = wp_get_attachment_image_src($img_id, 'thumbnail');
                        $img_src = $img_src[0];
                        echo '<li class="selected-item">';
                        echo '<img data-pic-id="' . esc_attr($img_id) . '" src="' . esc_url($img_src) . '" />';
                        echo '<a class="option-btn button-delete dashicons dashicons-trash" data-tooltip="' . esc_html__('Delete', 'ovs') . '" href="#"></a>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
                echo '</div>';

            } else {
                echo 'Erreur, l\'identifiant du champ est obligatoire. Vérifiez qu\'il ne soit pas vide.';
            }
        }
    }
}
