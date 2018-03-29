<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_Color_Gradient
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_color_gradient' ) ) {

    /**
     * Main ReduxFramework_color_gradient class
     *
     * @since       1.0.0
     */
    class ReduxFramework_color_gradient {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            // No errors please
            $defaults = array(
                'from' => '',
                'to'   => ''
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            echo '<div class="colorGradient"><strong>' . __( 'From ', 'redux-framework' ) . '</strong>&nbsp;';
            echo '<input data-id="' . $this->field['id'] . '" id="' . $this->field['id'] . '-from" name="' . $this->field['name'] . $this->field['name_suffix'] . '[from]' . '" value="' . $this->value['from'] . '" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['from'] . '" />';
            echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value="">';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value['from'] == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . $this->field['id'] . '-from-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency ' . $this->field['class'] . '" id="' . $this->field['id'] . '-from-transparency" data-id="' . $this->field['id'] . '-from" value="1"' . $tChecked . '> ' . __( 'Transparent', 'redux-framework' ) . '</label>';
            }
            echo "</div>";
            echo '<div class="colorGradient toLabel"><strong>' . __( 'To ', 'redux-framework' ) . '</strong>&nbsp;<input data-id="' . $this->field['id'] . '" id="' . $this->field['id'] . '-to" name="' . $this->field['name'] . $this->field['name_suffix'] . '[to]' . '" value="' . $this->value['to'] . '" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['to'] . '" />';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value['to'] == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . $this->field['id'] . '-to-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency" id="' . $this->field['id'] . '-to-transparency" data-id="' . $this->field['id'] . '-to" value="1"' . $tChecked . '> ' . __( 'Transparent', 'redux-framework' ) . '</label>';
            }
            echo "</div>";
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            wp_enqueue_style( 'wp-color-picker' );
            
            wp_enqueue_script(
                'redux-field-color-gradient-js',
                ReduxFramework::$_url . 'inc/fields/color_gradient/field_color_gradient' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'wp-color-picker', 'redux-js' ),
                time(),
                'all'
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style( 'redux-color-picker-css' );
                
                wp_enqueue_style(
                    'redux-field-color_gradient-css',
                    ReduxFramework::$_url . 'inc/fields/color_gradient/field_color_gradient.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }
    }
}