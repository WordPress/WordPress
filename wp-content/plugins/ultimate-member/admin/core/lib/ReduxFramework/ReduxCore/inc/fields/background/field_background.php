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
     * @subpackage  Field_Background
     * @author      Dovy Paukstys
     * @version     3.1.5
     */
// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

// Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_background' ) ) {

        /**
         * Main ReduxFramework_background class
         *
         * @since       3.1.5
         */
        class ReduxFramework_background {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since       3.1.5
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

                $defaults = array(
                    'background-color'      => true,
                    'background-repeat'     => true,
                    'background-attachment' => true,
                    'background-position'   => true,
                    'background-image'      => true,
                    'background-gradient'   => false,
                    'background-clip'       => false,
                    'background-origin'     => false,
                    'background-size'       => true,
                    'preview_media'         => false,
                    'preview'               => true,
                    'preview_height'        => '200px',
                    'transparent'           => true,
                );

                $this->field = wp_parse_args( $this->field, $defaults );

                // No errors please
                $defaults = array(
                    'background-color'      => '',
                    'background-repeat'     => '',
                    'background-attachment' => '',
                    'background-position'   => '',
                    'background-image'      => '',
                    'background-clip'       => '',
                    'background-origin'     => '',
                    'background-size'       => '',
                    'media'                 => array(),
                );

                $this->value = wp_parse_args( $this->value, $defaults );

                $defaults = array(
                    'id'        => '',
                    'width'     => '',
                    'height'    => '',
                    'thumbnail' => '',
                );

                $this->value['media'] = wp_parse_args( $this->value['media'], $defaults );

                // select2 args
                if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                    $select2_params = json_encode( $this->field['select2'] );
                    $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

                    echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
                }

                if ( $this->field['background-color'] === true ) {

                    if ( isset( $this->value['color'] ) && empty( $this->value['background-color'] ) ) {
                        $this->value['background-color'] = $this->value['color'];
                    }

                    echo '<input data-id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-color]" id="' . $this->field['id'] . '-color" class="redux-color redux-background-input redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $this->value['background-color'] . '"  data-default-color="' . ( isset( $this->field['default']['background-color'] ) ? $this->field['default']['background-color'] : "" ) . '" />';
                    echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value="">';

                    if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                        $tChecked = "";
                        if ( $this->value['background-color'] == "transparent" ) {
                            $tChecked = ' checked="checked"';
                        }
                        echo '<label for="' . $this->field['id'] . '-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency redux-background-input ' . $this->field['class'] . '" id="' . $this->field['id'] . '-transparency" data-id="' . $this->field['id'] . '-color" value="1"' . $tChecked . '> ' . __( 'Transparent', 'redux-framework' ) . '</label>';
                    }

                    if ( $this->field['background-repeat'] === true || $this->field['background-position'] === true || $this->field['background-attachment'] === true ) {
                        echo '<br />';
                    }
                }


                if ( $this->field['background-repeat'] === true ) {
                    $array = array(
                        'no-repeat' => 'No Repeat',
                        'repeat'    => 'Repeat All',
                        'repeat-x'  => 'Repeat Horizontally',
                        'repeat-y'  => 'Repeat Vertically',
                        'inherit'   => 'Inherit',
                    );
                    echo '<select id="' . $this->field['id'] . '-repeat-select" data-placeholder="' . __( 'Background Repeat', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-repeat]" class="redux-select-item redux-background-input redux-background-repeat ' . $this->field['class'] . '">';
                    echo '<option></option>';

                    foreach ( $array as $k => $v ) {
                        echo '<option value="' . $k . '"' . selected( $this->value['background-repeat'], $k, false ) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                }

                if ( $this->field['background-clip'] === true ) {
                    $array = array(
                        'inherit'     => 'Inherit',
                        'border-box'  => 'Border Box',
                        'content-box' => 'Content Box',
                        'padding-box' => 'Padding Box',
                    );
                    echo '<select id="' . $this->field['id'] . '-repeat-select" data-placeholder="' . __( 'Background Clip', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-clip]" class="redux-select-item redux-background-input redux-background-clip ' . $this->field['class'] . '">';
                    echo '<option></option>';

                    foreach ( $array as $k => $v ) {
                        echo '<option value="' . $k . '"' . selected( $this->value['background-clip'], $k, false ) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                }

                if ( $this->field['background-origin'] === true ) {
                    $array = array(
                        'inherit'     => 'Inherit',
                        'border-box'  => 'Border Box',
                        'content-box' => 'Content Box',
                        'padding-box' => 'Padding Box',
                    );
                    echo '<select id="' . $this->field['id'] . '-repeat-select" data-placeholder="' . __( 'Background Origin', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-origin]" class="redux-select-item redux-background-input redux-background-origin ' . $this->field['class'] . '">';
                    echo '<option></option>';

                    foreach ( $array as $k => $v ) {
                        echo '<option value="' . $k . '"' . selected( $this->value['background-origin'], $k, false ) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                }

                if ( $this->field['background-size'] === true ) {
                    $array = array(
                        'inherit' => 'Inherit',
                        'cover'   => 'Cover',
                        'contain' => 'Contain',
                    );
                    echo '<select id="' . $this->field['id'] . '-repeat-select" data-placeholder="' . __( 'Background Size', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-size]" class="redux-select-item redux-background-input redux-background-size ' . $this->field['class'] . '">';
                    echo '<option></option>';

                    foreach ( $array as $k => $v ) {
                        echo '<option value="' . $k . '"' . selected( $this->value['background-size'], $k, false ) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                }

                if ( $this->field['background-attachment'] === true ) {
                    $array = array(
                        'fixed'   => 'Fixed',
                        'scroll'  => 'Scroll',
                        'inherit' => 'Inherit',
                    );
                    echo '<select id="' . $this->field['id'] . '-attachment-select" data-placeholder="' . __( 'Background Attachment', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-attachment]" class="redux-select-item redux-background-input redux-background-attachment ' . $this->field['class'] . '">';
                    echo '<option></option>';
                    foreach ( $array as $k => $v ) {
                        echo '<option value="' . $k . '"' . selected( $this->value['background-attachment'], $k, false ) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                }

                if ( $this->field['background-position'] === true ) {
                    $array = array(
                        'left top'      => 'Left Top',
                        'left center'   => 'Left center',
                        'left bottom'   => 'Left Bottom',
                        'center top'    => 'Center Top',
                        'center center' => 'Center Center',
                        'center bottom' => 'Center Bottom',
                        'right top'     => 'Right Top',
                        'right center'  => 'Right center',
                        'right bottom'  => 'Right Bottom',
                    );
                    echo '<select id="' . $this->field['id'] . '-position-select" data-placeholder="' . __( 'Background Position', 'redux-framework' ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-position]" class="redux-select-item redux-background-input redux-background-position ' . $this->field['class'] . '">';
                    echo '<option></option>';

                    foreach ( $array as $k => $v ) {
                        echo '<option value="' . $k . '"' . selected( $this->value['background-position'], $k, false ) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                }

                if ( $this->field['background-image'] === true ) {
                    echo '<br />';

                    if ( empty( $this->value ) && ! empty( $this->field['default'] ) ) { // If there are standard values and value is empty
                        if ( is_array( $this->field['default'] ) ) {
                            if ( ! empty( $this->field['default']['media']['id'] ) ) {
                                $this->value['media']['id'] = $this->field['default']['media']['id'];
                            } else if ( ! empty( $this->field['default']['id'] ) ) {
                                $this->value['media']['id'] = $this->field['default']['id'];
                            }

                            if ( ! empty( $this->field['default']['url'] ) ) {
                                $this->value['background-image'] = $this->field['default']['url'];
                            } else if ( ! empty( $this->field['default']['media']['url'] ) ) {
                                $this->value['background-image'] = $this->field['default']['media']['url'];
                            } else if ( ! empty( $this->field['default']['background-image'] ) ) {
                                $this->value['background-image'] = $this->field['default']['background-image'];
                            }
                        } else {
                            if ( is_numeric( $this->field['default'] ) ) { // Check if it's an attachment ID
                                $this->value['media']['id'] = $this->field['default'];
                            } else { // Must be a URL
                                $this->value['background-image'] = $this->field['default'];
                            }
                        }
                    }


                    if ( empty( $this->value['background-image'] ) && ! empty( $this->value['media']['id'] ) ) {
                        $img                             = wp_get_attachment_image_src( $this->value['media']['id'], 'full' );
                        $this->value['background-image'] = $img[0];
                        $this->value['media']['width']   = $img[1];
                        $this->value['media']['height']  = $img[2];
                    }

                    $hide = 'hide ';

                    if ( ( isset( $this->field['preview_media'] ) && $this->field['preview_media'] === false ) ) {
                        $this->field['class'] .= " noPreview";
                    }

                    if ( ( ! empty( $this->field['background-image'] ) && $this->field['background-image'] === true ) || isset( $this->field['preview'] ) && $this->field['preview'] === false ) {
                        $hide = '';
                    }

                    $placeholder = isset( $this->field['placeholder'] ) ? $this->field['placeholder'] : __( 'No media selected', 'redux-framework' );

                    echo '<input placeholder="' . $placeholder . '" type="text" class="redux-background-input ' . $hide . 'upload ' . $this->field['class'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[background-image]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][background-image]" value="' . $this->value['background-image'] . '" />';
                    echo '<input type="hidden" class="upload-id ' . $this->field['class'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[media][id]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][media][id]" value="' . $this->value['media']['id'] . '" />';
                    echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . $this->field['name_suffix'] . '[media][height]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][media][height]" value="' . $this->value['media']['height'] . '" />';
                    echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . $this->field['name_suffix'] . '[media][width]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][media][width]" value="' . $this->value['media']['width'] . '" />';
                    echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . $this->field['name_suffix'] . '[media][thumbnail]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][media][thumbnail]" value="' . $this->value['media']['thumbnail'] . '" />';

                    //Preview
                    $hide = '';

                    if ( ( isset( $this->field['preview_media'] ) && $this->field['preview_media'] === false ) || empty( $this->value['background-image'] ) ) {
                        $hide = 'hide ';
                    }

                    if ( empty( $this->value['media']['thumbnail'] ) && ! empty( $this->value['background-image'] ) ) { // Just in case
                        if ( ! empty( $this->value['media']['id'] ) ) {
                            $image                             = wp_get_attachment_image_src( $this->value['media']['id'], array(
                                    150,
                                    150
                                ) );
                            $this->value['media']['thumbnail'] = $image[0];
                        } else {
                            $this->value['media']['thumbnail'] = $this->value['background-image'];
                        }
                    }

                    echo '<div class="' . $hide . 'screenshot">';
                    echo '<a class="of-uploaded-image" href="' . $this->value['background-image'] . '" target="_blank">';
                    echo '<img class="redux-option-image" id="image_' . $this->value['media']['id'] . '" src="' . $this->value['media']['thumbnail'] . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    //Upload controls DIV
                    echo '<div class="upload_button_div">';

                    //If the user has WP3.5+ show upload/remove button
                    echo '<span class="button redux-background-upload" id="' . $this->field['id'] . '-media">' . __( 'Upload', 'redux-framework' ) . '</span>';

                    $hide = '';
                    if ( empty( $this->value['background-image'] ) || $this->value['background-image'] == '' ) {
                        $hide = ' hide';
                    }

                    echo '<span class="button removeCSS redux-remove-background' . $hide . '" id="reset_' . $this->field['id'] . '" rel="' . $this->field['id'] . '">' . __( 'Remove', 'redux-framework' ) . '</span>';

                    echo '</div>';
                }


                /**
                 * Preview
                 * */
                if ( ! isset( $this->field['preview'] ) || $this->field['preview'] !== false ):

                    $css = $this->getCSS();
                    if ( empty( $css ) ) {
                        $css = "display:none;";
                    }
                    $css .= "height: " . $this->field['preview_height'] . ";";
                    echo '<p class="clear ' . $this->field['id'] . '_previewer background-preview" style="' . $css . '">&nbsp;</p>';

                endif;
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
                if ( function_exists( 'wp_enqueue_media' ) ) {
                    wp_enqueue_media();
                } else {
                    if (!wp_script_is ( 'media-upload' )) {
                        wp_enqueue_script( 'media-upload' );
                    }
                }
                
                if (!wp_style_is ( 'select2-css' )) {
                    wp_enqueue_style( 'select2-css' );
                }
                
                if (!wp_style_is ( 'wp-color-picker' )) {
                    wp_enqueue_style( 'wp-color-picker' );
                }
                
                if (!wp_script_is ( 'redux-field-background-js' )) {
                    wp_enqueue_script(
                        'redux-field-background-js',
                        ReduxFramework::$_url . 'inc/fields/background/field_background' . Redux_Functions::isMin() . '.js',
                        array( 'jquery', 'wp-color-picker', 'select2-js', 'redux-js' ),
                        time(),
                        true
                    );
                }

                if ($this->parent->args['dev_mode']) {
                    if (!wp_style_is ( 'redux-field-background-css' )) {
                        wp_enqueue_style(
                            'redux-field-background-css',
                            ReduxFramework::$_url . 'inc/fields/background/field_background.css',
                            array(),
                            time(),
                            'all'
                        );
                    }
                    
                    if (!wp_style_is ( 'redux-color-picker-css' )) {
                        wp_enqueue_style( 'redux-color-picker-css' );
                    }
                }
            }

            public static function getCSS( $value = array() ) {

                $css = '';

                if ( ! empty( $value ) && is_array( $value ) ) {
                    foreach ( $value as $key => $value ) {
                        if ( ! empty( $value ) && $key != "media" ) {
                            if ( $key == "background-image" ) {
                                $css .= $key . ":url('" . $value . "');";
                            } else {
                                $css .= $key . ":" . $value . ";";
                            }
                        }
                    }
                }

                return $css;
            }

            public function output() {
                $style = $this->getCSS( $this->value );

                if ( ! empty( $style ) ) {

                    if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                        $keys = implode( ",", $this->field['output'] );
                        $this->parent->outputCSS .= $keys . "{" . $style . '}';
                    }

                    if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                        $keys = implode( ",", $this->field['compiler'] );
                        $this->parent->compilerCSS .= $keys . "{" . $style . '}';
                    }
                }
            }
        }
    }
