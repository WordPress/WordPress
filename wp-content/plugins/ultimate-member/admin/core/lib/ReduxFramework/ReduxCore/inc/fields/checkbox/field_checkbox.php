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
 * @subpackage  Field_Checkbox
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */
// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( !class_exists ( 'ReduxFramework_checkbox' ) ) {

    /**
     * Main ReduxFramework_checkbox class
     *
     * @since       1.0.0
     */
    class ReduxFramework_checkbox {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct ( $field = array(), $value = '', $parent ) {

            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render () {
            if( !empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
                if (empty($this->field['args'])) {
                    $this->field['args'] = array();
                }

                $this->field['options'] = $this->parent->get_wordpress_data($this->field['data'], $this->field['args']);
                if (empty($this->field['options'])) {
                    return;
                }
            }

            $this->field[ 'data_class' ] = ( isset ( $this->field[ 'multi_layout' ] ) ) ? 'data-' . $this->field[ 'multi_layout' ] : 'data-full';

            if ( !empty ( $this->field[ 'options' ] ) && ( is_array ( $this->field[ 'options' ] ) || is_array ( $this->field[ 'default' ] ) ) ) {

                echo '<ul class="' . $this->field[ 'data_class' ] . '">';

                if ( !isset ( $this->value ) ) {
                    $this->value = array();
                }

                if ( !is_array ( $this->value ) ) {
                    $this->value = array();
                }

                if ( empty ( $this->field[ 'options' ] ) && isset ( $this->field[ 'default' ] ) && is_array ( $this->field[ 'default' ] ) ) {
                    $this->field[ 'options' ] = $this->field[ 'default' ];
                }

                foreach ( $this->field[ 'options' ] as $k => $v ) {

                    if ( empty ( $this->value[ $k ] ) ) {
                        $this->value[ $k ] = "";
                    }

                    echo '<li>';
                    echo '<label for="' . strtr ( $this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ] . '][' . $k . ']', array(
                        '[' => '_',
                        ']' => ''
                    ) ) . '_' . array_search ( $k, array_keys ( $this->field[ 'options' ] ) ) . '">';
                    echo '<input type="hidden" class="checkbox-check" data-val="1" name="' . $this->field[ 'name' ] . '[' . $k . ']' . $this->field[ 'name_suffix' ] . '" value="' . $this->value[ $k ] . '" ' . '/>';
                    echo '<input type="checkbox" class="checkbox ' . $this->field[ 'class' ] . '" id="' . strtr ( $this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ] . '][' . $k . ']', array(
                        '[' => '_',
                        ']' => ''
                    ) ) . '_' . array_search ( $k, array_keys ( $this->field[ 'options' ] ) ) . '" value="1" ' . checked ( $this->value[ $k ], '1', false ) . '/>';
                    echo ' ' . $v . '</label>';
                    echo '</li>';
                }

                echo '</ul>';
            } else if ( empty ( $this->field[ 'data' ] ) ) {

                echo (!empty ( $this->field[ 'desc' ] ) ) ? ' <ul class="data-full"><li><label for="' . strtr ( $this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ] . ']', array(
                            '[' => '_',
                            ']' => ''
                        ) ) . '">' : '';

                // Got the "Checked" status as "0" or "1" then insert it as the "value" option
                //$ch_value = 1; // checked($this->value, '1', false) == "" ? "0" : "1";
                echo '<input type="hidden" class="checkbox-check" data-val="1" name="' . $this->field[ 'name' ] . $this->field[ 'name_suffix' ] . '" value="' . $this->value . '" ' . '/>';
                echo '<input type="checkbox" id="' . strtr ( $this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ] . ']', array(
                    '[' => '_',
                    ']' => ''
                ) ) . '" value="1" class="checkbox ' . $this->field[ 'class' ] . '" ' . checked ( $this->value, '1', false ) . '/>';
                echo isset( $this->field[ 'label' ] ) ? ' ' . $this->field[ 'label' ] : '';
                echo '</label></li></ul>';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue () {

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style (
                    'redux-field-checkbox-css',
                    ReduxFramework::$_url . 'inc/fields/checkbox/field_checkbox.css',
                    array(),
                    time (),
                    'all'
                );
            }

            wp_enqueue_script (
                'redux-field-checkbox-js',
                ReduxFramework::$_url . 'inc/fields/checkbox/field_checkbox' . Redux_Functions::isMin () . '.js',
                array( 'jquery', 'redux-js' ),
                time (),
                true
            );
        }
    }

}
