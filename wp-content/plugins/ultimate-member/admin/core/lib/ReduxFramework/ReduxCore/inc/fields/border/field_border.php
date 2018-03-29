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
 * @subpackage  Border
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_border' ) ) {
    
    class ReduxFramework_border {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 1.0.0
         */
        function __construct( $field = array(), $value = '', $parent ) {

            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        } //function

        private function stripAlphas($s) {

            // Regex is our friend.  THERE ARE FOUR LIGHTS!!
            return preg_replace('/[^\d.-]/', '', $s);
        }             

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {

            // No errors please
            $defaults = array(
                'top'    => true,
                'bottom' => true,
                'all'    => true,
                'style'  => true,
                'color'  => true,
                'left'   => true,
                'right'  => true,
            );

            $this->field = wp_parse_args( $this->field, $defaults );

            $defaults = array(
                'top'    => '',
                'right'  => '',
                'bottom' => '',
                'left'   => '',
                'color'  => '',
                'style'  => '',
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            $value = array(
                'top'    => isset( $this->value['border-top'] ) ? filter_var( $this->value['border-top'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['top'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'right'  => isset( $this->value['border-right'] ) ? filter_var( $this->value['border-right'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['right'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'bottom' => isset( $this->value['border-bottom'] ) ? filter_var( $this->value['border-bottom'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['bottom'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'left'   => isset( $this->value['border-left'] ) ? filter_var( $this->value['border-left'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['left'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'color'  => isset( $this->value['border-color'] ) ? $this->value['border-color'] : $this->value['color'],
                'style'  => isset( $this->value['border-style'] ) ? $this->value['border-style'] : $this->value['style']
            );

            if ( ( isset( $this->value['width'] ) || isset( $this->value['border-width'] ) ) ) {
                if ( isset( $this->value['border-width'] ) && ! empty( $this->value['border-width'] ) ) {
                    $this->value['width'] = $this->value['border-width'];
                }

                $this->value['width'] = $this->stripAlphas($this->value['width']);

                $value['top']    = $this->value['width'];
                $value['right']  = $this->value['width'];
                $value['bottom'] = $this->value['width'];
                $value['left']   = $this->value['width'];
            }

            $this->value = $value;

            $defaults = array(
                'top'    => '',
                'right'  => '',
                'bottom' => '',
                'left'   => '',
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                $select2_params = json_encode( $this->field['select2'] );
                $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

                echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
            }


            echo '<input type="hidden" class="field-units" value="px">';

            if ( isset( $this->field['all'] ) && $this->field['all'] == true ) {
                echo '<div class="field-border-input input-prepend"><span class="add-on"><i class="el el-fullscreen icon-large"></i></span><input type="text" class="redux-border-all redux-border-input mini ' . $this->field['class'] . '" placeholder="' . __( 'All', 'redux-framework' ) . '" rel="' . $this->field['id'] . '-all" value="' . $this->value['top'] . '"></div>';
            }

            echo '<input type="hidden" class="redux-border-value" id="' . $this->field['id'] . '-top" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-top]" value="' . ( isset($this->value['top']) && $this->value['top'] != '' ? $this->value['top'] . 'px' : '' ) . '">';
            echo '<input type="hidden" class="redux-border-value" id="' . $this->field['id'] . '-right" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-right]" value="' . ( isset($this->value['right']) && $this->value['right'] != '' ? $this->value['right'] . 'px' : '' ) . '">';
            echo '<input type="hidden" class="redux-border-value" id="' . $this->field['id'] . '-bottom" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-bottom]" value="' . ( isset($this->value['bottom']) && $this->value['bottom'] != '' ? $this->value['bottom'] . 'px' : '' ) . '">';
            echo '<input type="hidden" class="redux-border-value" id="' . $this->field['id'] . '-left" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-left]" value="' . ( isset($this->value['left']) && $this->value['left'] != '' ? $this->value['left'] . 'px' : '' ) . '">';

            if ( ! isset( $this->field['all'] ) || $this->field['all'] !== true ) {
                /**
                 * Top
                 * */
                if ( $this->field['top'] === true ) {
                    echo '<div class="field-border-input input-prepend"><span class="add-on"><i class="el el-arrow-up icon-large"></i></span><input type="text" class="redux-border-top redux-border-input mini ' . $this->field['class'] . '" placeholder="' . __( 'Top', 'redux-framework' ) . '" rel="' . $this->field['id'] . '-top" value="' . $this->value['top'] . '"></div>';
                }

                /**
                 * Right
                 * */
                if ( $this->field['right'] === true ) {
                    echo '<div class="field-border-input input-prepend"><span class="add-on"><i class="el el-arrow-right icon-large"></i></span><input type="text" class="redux-border-right redux-border-input mini ' . $this->field['class'] . '" placeholder="' . __( 'Right', 'redux-framework' ) . '" rel="' . $this->field['id'] . '-right" value="' . $this->value['right'] . '"></div>';
                }

                /**
                 * Bottom
                 * */
                if ( $this->field['bottom'] === true ) {
                    echo '<div class="field-border-input input-prepend"><span class="add-on"><i class="el el-arrow-down icon-large"></i></span><input type="text" class="redux-border-bottom redux-border-input mini ' . $this->field['class'] . '" placeholder="' . __( 'Bottom', 'redux-framework' ) . '" rel="' . $this->field['id'] . '-bottom" value="' . $this->value['bottom'] . '"></div>';
                }

                /**
                 * Left
                 * */
                if ( $this->field['left'] === true ) {
                    echo '<div class="field-border-input input-prepend"><span class="add-on"><i class="el el-arrow-left icon-large"></i></span><input type="text" class="redux-border-left redux-border-input mini ' . $this->field['class'] . '" placeholder="' . __( 'Left', 'redux-framework' ) . '" rel="' . $this->field['id'] . '-left" value="' . $this->value['left'] . '"></div>';
                }
            }

            /**
             * Border-style
             * */
            if ( $this->field['style'] != false ) {
                $options = array(
                    'solid'  => 'Solid',
                    'dashed' => 'Dashed',
                    'dotted' => 'Dotted',
                    'double' => "Double",
                    'none'   => 'None'
                );
                echo '<select original-title="' . __( 'Border style', 'redux-framework' ) . '" id="' . $this->field['id'] . '[border-style]" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-style]" class="tips redux-border-style ' . $this->field['class'] . '" rows="6" data-id="' . $this->field['id'] . '">';
                foreach ( $options as $k => $v ) {
                    echo '<option value="' . $k . '"' . selected( $value['style'], $k, false ) . '>' . $v . '</option>';
                }
                echo '</select>';
            } else {
                echo '<input type="hidden" id="' . $this->field['id'] . '[border-style]" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-style]" value="' . $this->value['style'] . '" data-id="' . $this->field['id'] . '">';
            }

            /**
             * Color
             * */
            if ( $this->field['color'] != false ) {
                $default = isset( $this->field['default']['border-color'] ) ? $this->field['default']['border-color'] : '';


                if ( empty( $default ) ) {
                    $default = ( isset( $this->field['default']['color'] ) ) ? $this->field['default']['color'] : '#ffffff';
                }

                echo '<input name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-color]" id="' . $this->field['id'] . '-border" class="redux-border-color redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $this->value['color'] . '"  data-default-color="' . $default . '" data-id="' . $this->field['id'] . '" />';
            } else {
                echo '<input type="hidden" id="' . $this->field['id'] . '[border-color]" name="' . $this->field['name'] . $this->field['name_suffix'] . '[border-color]" value="' . $this->value['color'] . '" data-id="' . $this->field['id'] . '">';
            }
        }

        //function

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        function enqueue() {
            $min = Redux_Functions::isMin();
            
            if (!wp_style_is ( 'select2-css' )) {
                wp_enqueue_style( 'select2-css' );
            }
            
            if (!wp_style_is ( 'wp-color-picker' )) {
                wp_enqueue_style( 'wp-color-picker' );
            }
            
            if (!wp_script_is ( 'redux-field-border-js' )) {
                wp_enqueue_script(
                    'redux-field-border-js',
                    ReduxFramework::$_url . 'inc/fields/border/field_border' . $min . '.js',
                    array( 'jquery', 'select2-js', 'wp-color-picker', 'redux-js' ),
                    time(),
                    true
                );
            }

            if ($this->parent->args['dev_mode']) {
                if (!wp_style_is ( 'redux-color-picker-css' )) {
                    wp_enqueue_style( 'redux-color-picker-css' );
                }
                
                if (!wp_style_is ( 'redux-field-border-css' )) {
                    wp_enqueue_style(
                        'redux-field-border-css',
                        ReduxFramework::$_url . 'inc/fields/border/field_border.css',
                        array(),
                        time(),
                        'all'
                    );
                }
            }
        } //function

        public function output() {
            if ( isset( $this->field['all'] ) && true == $this->field['all'] ) {
                $borderWidth = isset( $this->value['border-width'] ) ? $this->value['border-width'] : '0px';
                $val         = isset( $this->value['border-top'] ) ? $this->value['border-top'] : $borderWidth;

                $this->value['border-top']    = $val;
                $this->value['border-bottom'] = $val;
                $this->value['border-left']   = $val;
                $this->value['border-right']  = $val;
            }

            $cleanValue = array(
                'color' => ! empty( $this->value['border-color'] ) ? $this->value['border-color'] : '',
                'style' => ! empty( $this->value['border-style'] ) ? $this->value['border-style'] : ''
            );

            $borderWidth = '';
            if ( isset( $this->value['border-width'] ) ) {
                $borderWidth = $this->value['border-width'];
            }

            $this->field['top']    = isset( $this->field['top'] ) ? $this->field['top'] : true;
            $this->field['bottom'] = isset( $this->field['bottom'] ) ? $this->field['bottom'] : true;
            $this->field['left']   = isset( $this->field['left'] ) ? $this->field['left'] : true;
            $this->field['right']  = isset( $this->field['right'] ) ? $this->field['right'] : true;

            if ( $this->field['top'] === true ) {
                $cleanValue['top'] = ! empty( $this->value['border-top'] ) ? $this->value['border-top'] : $borderWidth;
            }

            if ( $this->field['bottom'] == true ) {
                $cleanValue['bottom'] = ! empty( $this->value['border-bottom'] ) ? $this->value['border-bottom'] : $borderWidth;
            }

            if ( $this->field['left'] === true ) {
                $cleanValue['left'] = ! empty( $this->value['border-left'] ) ? $this->value['border-left'] : $borderWidth;
            }

            if ( $this->field['right'] === true ) {
                $cleanValue['right'] = ! empty( $this->value['border-right'] ) ? $this->value['border-right'] : $borderWidth;
            }

            $style = "";

            //absolute, padding, margin
            if ( ! isset( $this->field['all'] ) || $this->field['all'] != true ) {
                foreach ( $cleanValue as $key => $value ) {
                    if ( $key == "color" || $key == "style" ) {
                        continue;
                    }
                    if (!empty($value)) {
                        $style .= 'border-' . $key . ':' . $value . ' ' . $cleanValue['style'] . ' ' . $cleanValue['color'] . ';';
                    }
                }
            } else {
                if (!empty($cleanValue['top'])) {
                    $style .= 'border:' . $cleanValue['top'] . ' ' . $cleanValue['style'] . ' ' . $cleanValue['color'] . ';';
                }
            }

            if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                $keys = implode( ",", $this->field['output'] );
                
                if (!empty($style)) {
                    $this->parent->outputCSS .= $keys . "{" . $style . '}';
                }
            }

            if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                $keys = implode( ",", $this->field['compiler'] );
                
                if (!empty($style)) {
                    $this->parent->compilerCSS .= $keys . "{" . $style . '}';
                }
            }
        }
    } //class
}