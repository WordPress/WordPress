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
     * @package     Redux_Field
     * @subpackage  Button_Set
     * @author      Daniel J Griffiths (Ghost1227)
     * @author      Dovy Paukstys
     * @version     3.0.0
     */

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

// Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_button_set' ) ) {

        /**
         * Main ReduxFramework_button_set class
         *
         * @since       1.0.0
         */
        class ReduxFramework_button_set {

            /**
             * Holds configuration settings for each field in a model.
             * Defining the field options
             * array['fields']              array Defines the fields to be shown by scaffolding.
             *          [fieldName]         array Defines the options for a field, or just enables the field if array is not applied.
             *              ['name']        string Overrides the field name (default is the array key)
             *              ['model']       string (optional) Overrides the model if the field is a belongsTo associated value.
             *              ['width']       string Defines the width of the field for paginate views. Examples are "100px" or "auto"
             *              ['align']       string Alignment types for paginate views (left, right, center)
             *              ['format']      string Formatting options for paginate fields. Options include ('currency','nice','niceShort','timeAgoInWords' or a valid Date() format)
             *              ['title']       string Changes the field name shown in views.
             *              ['desc']        string The description shown in edit/create views.
             *              ['readonly']    boolean True prevents users from changing the value in edit/create forms.
             *              ['type']        string Defines the input type used by the Form helper (example 'password')
             *              ['options']     array Defines a list of string options for drop down lists.
             *              ['editor']      boolean If set to True will show a WYSIWYG editor for this field.
             *              ['default']     string The default value for create forms.
             *
             * @param array $arr (See above)
             *
             * @return Object A new editor object.
             * */
            static $_properties = array(
                'id' => 'Identifier',
            );

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
                if ( !empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
                    if ( empty( $this->field['args'] ) ) {
                        $this->field['args'] = array();
                    }

                    $this->field['options'] = $this->parent->get_wordpress_data( $this->field['data'], $this->field['args'] );

                    if ( empty( $this->field['options'] ) ) {
                        return;
                    }
                }

                // multi => true renders the field multi-selectable (checkbox vs radio)
                echo '<div class="buttonset ui-buttonset">';
                $i = 0;
                foreach ( $this->field['options'] as $k => $v ) {

                    $selected = '';
                    if ( isset( $this->field['multi'] ) && $this->field['multi'] == true ) {
                        $type         = "checkbox";
                        $multi_suffix = '[]';

                        if ( ! empty( $this->value ) && ! is_array( $this->value ) ) {
                            $this->value = array( $this->value );
                        }

                        if ( is_array( $this->value ) && in_array( $k, $this->value ) ) {
                            $selected = 'checked="checked"';
                        }

                    } else {
                        $multi_suffix = "";
                        $type         = "radio";

                        if ( is_scalar( $this->value ) ) {
                            $selected = checked( $this->value, $k, false );
                        }
                    }

                    echo '<input data-id="' . $this->field['id'] . '" type="' . $type . '" id="' . $this->field['id'] . '-buttonset' . $k . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $multi_suffix . '" class="buttonset-item ' . $this->field['class'] . '" value="' . $k . '" ' . $selected . '/>';
                    echo '<label for="' . $this->field['id'] . '-buttonset' . $k . '">' . $v . '</label>';
                }

                echo '</div>';
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

                if (!wp_script_is ( 'redux-field-button-set-js' )) {
                    wp_enqueue_script(
                        'redux-field-button-set-js',
                        ReduxFramework::$_url . 'inc/fields/button_set/field_button_set' . Redux_Functions::isMin() . '.js',
                        array( 'jquery', 'jquery-ui-core', 'redux-js' ),
                        time(),
                        true
                    );
                }
            }
        }
    }