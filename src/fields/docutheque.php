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
if(!class_exists('Field_docutheque')) {
    class Field_docutheque extends Field
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
                echo '<button class="button-upload-multi" data-field="' . esc_attr($this->getId()) . '" data-multiple="true" data-type="application/msword,
							application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							application/vnd.ms-word.document.macroEnabled.12,
							application/vnd.ms-word.template.macroEnabled.12,
							application/vnd.oasis.opendocument.text,
							application/vnd.apple.pages,
							application/pdf,
							application/vnd.ms-xpsdocument,
							application/oxps,
							application/rtf,
							application/wordperfect,
							application/octet-stream" >Upload Media</button>';

                echo '<button class="button-delete-all" title="Delete">' . esc_html__('Delete all', 'ovs') . '</button>';

                if (!empty($this->getSub_desc())) {
                    echo '<p>' . esc_html($this->getSub_desc()) . '</p>';
                }
                echo '</div>';
                echo '<ul class="gallery-container clearfix">';
                if (!empty($this->getValue())) {
                    $images = explode(',', $this->getValue());

                    foreach ($images as $img_id) {

                        echo '<li class="selected-item">';
                        echo '<div class="img">';
                        echo '<img data-pic-id="' . esc_attr($img_id) . '" src="/wp-includes/images/media/document.png" class="icon" draggable="false" alt="" />';
                        echo '</div>';
                        echo '<div class="filename">';
                        echo get_the_title($img_id);
                        echo '</div>';
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
