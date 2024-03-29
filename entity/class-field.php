<?php

namespace Ovs\ClassField;

/**
 * Custom Field
 *
 * @package OVS
 * @author Clément Vacheron
 * @link https://www.overscan.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Field')) {
    class Field
    {
        protected $id = ''; // Identifiant du champs
        protected $name; // Nom du champs
        protected $label; // Libellé du champs
        protected $type = 'text'; // Type du champs
        protected $value = false; // Valeur du champs
        protected $placeholder = ''; // Valeur du champs
        protected $required = false; // Champs obligatoire ou non
        protected $sub_desc = ''; // courte description sur le champs, indication de la valuer attendu..
        protected $column = false; // Définis si l'info doit apparaitre dans la liste de tous les éléments.

        /**
         * Get the value of id
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set the value of id
         *
         * @return  self
         */
        public function setId($id)
        {
            $this->id = $id;

            return $this;
        }

        /**
         * Get the value of name
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set the value of name
         *
         * @return  self
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get the value of label
         */
        public function getLabel()
        {
            return $this->label;
        }

        /**
         * Set the value of label
         *
         * @return  self
         */
        public function setLabel($label)
        {
            $this->label = $label;

            return $this;
        }

        /**
         * Get the value of type
         */
        public function getType()
        {
            return $this->type;
        }

        /**
         * Set the value of type
         *
         * @return  self
         */
        public function setType($type)
        {
            $this->type = $type;

            return $this;
        }

        /**
         * Get the value of value
         */
        public function getValue()
        {
            return $this->value;
        }

        /**
         * Set the value of value
         *
         * @return  self
         */
        public function setValue($value)
        {
            $this->value = $value;

            return $this;
        }

        /**
         * Get the value of required
         */
        public function getRequired()
        {
            return $this->required;
        }

        /**
         * Set the value of required
         *
         * @return  self
         */
        public function setRequired($required)
        {
            $this->required = $required;

            return $this;
        }

        /**
         * Get the value of sub_desc
         */
        public function getSub_desc()
        {
            return $this->sub_desc;
        }

        /**
         * Set the value of sub_desc
         *
         * @return  self
         */
        public function setSub_desc($sub_desc)
        {
            $this->sub_desc = $sub_desc;

            return $this;
        }
        /**
         * Get the value of placeholder
         */
        public function getPlaceholder()
        {
            return $this->placeholder;
        }
        /**
         * Get the value of column
         */
        public function getColumn()
        {
            return $this->column;
        }

        /**
         * Set the value of column
         *
         * @return  self
         */
        public function setColumn($column)
        {
            $this->column = $column;

            return $this;
        }

        /**
         * Set the value of placeholder
         *
         * @return  self
         */
        public function setPlaceholder($placeholder)
        {
            $this->placeholder = $placeholder;

            return $this;
        }
        public function __construct($field, $value = false)
        {
            $this->setId($field['id']);
            $this->setType($field['type']);
            $this->setLabel($field['label']);
            $this->setRequired(array_key_exists('required', $field) ? $field['required'] : false);
            $this->setName(array_key_exists('name', $field) ? $field['name'] : '');
            $this->setValue($value);
            $this->setSub_desc(array_key_exists('desc', $field) ? $field['desc'] : '');
            $this->setPlaceholder(array_key_exists('placeholder', $field) ? $field['placeholder'] : '');
            $this->setColumn(array_key_exists('column', $field) ? $field['column'] : false);
        }
        public function render()
        {

            //The way is closed, it was made by those who died; and the dead guard it… the way is closed
        }

        public function columnContent()
        {
            $content = '<div>' . $this->getValue() . '</div>';
            return $content;
        }
    }
}
