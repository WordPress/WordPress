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
     * @subpackage  Field_Info
     * @author      Daniel J Griffiths (Ghost1227)
     * @author      Dovy Paukstys
     * @version     3.0.0
     */

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

// Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_info' ) ) {

        /**
         * Main ReduxFramework_info class
         *
         * @since       1.0.0
         */
        class ReduxFramework_info {

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

                $defaults    = array(
                    'title'  => '',
                    'desc'   => '',
                    'notice' => true,
                    'style'  => '',
                    'color'  => '',
                );

                $this->field = wp_parse_args( $this->field, $defaults );

                $styles = array(
                    'normal',
                    'info',
                    'warning',
                    'success',
                    'critical',
                    'custom'
                );

                if (!in_array($this->field['style'], $styles)) {
                    $this->field['style'] = 'normal';
                }
                if ($this->field['style'] == "custom") {
                    if (!empty($this->field['color']) ) {
                        $this->field['color'] = "border-color:".$this->field['color'].';';
                    } else {
                        $this->field['style'] = 'normal';
                        $this->field['color'] = "";
                    }
                } else {
                    $this->field['color'] = "";
                }

                if ( empty( $this->field['desc'] ) && ! empty( $this->field['default'] ) ) {
                    $this->field['desc'] = $this->field['default'];
                    unset( $this->field['default'] );
                }

                if ( empty( $this->field['desc'] ) && ! empty( $this->field['subtitle'] ) ) {
                    $this->field['desc'] = $this->field['subtitle'];
                    unset( $this->field['subtitle'] );
                }

                if ( empty( $this->field['desc'] ) ) {
                    $this->field['desc'] = "";
                }

                if ( empty( $this->field['raw_html'] ) ) {
                    if ( $this->field['notice'] == true ) {
                        $this->field['class'] .= ' redux-notice-field';
                    } else {
                        $this->field['class'] .= ' redux-info-field';
                    }



                    $this->field['style'] = 'redux-' . $this->field['style'] . ' ';
                }

                $indent = ( isset( $this->field['sectionIndent'] ) && $this->field['sectionIndent'] ) ? ' form-table-section-indented' : '';

                echo '</td></tr></table><div id="info-' . esc_attr($this->field['id']) . '" class="' . ( isset( $this->field['icon'] ) && ! empty( $this->field['icon'] ) && $this->field['icon'] !== true ? "hasIcon " : "") . esc_attr($this->field['style']) . ' ' . esc_attr($this->field['class']) . ' redux-field-' . esc_attr($this->field['type']) . esc_attr($indent) . '"'.( !empty($this->field['color']) ? ' style="' . esc_attr($this->field['color']) . '"' : '' ) . '>';

                if ( ! empty( $this->field['raw_html'] ) && $this->field['raw_html'] ) {
                    echo wp_kses_post($this->field['desc']);
                } else {
                    if ( isset( $this->field['title'] ) && ! empty( $this->field['title'] ) ) {
                        $this->field['title'] = '<b>' . wp_kses_post($this->field['title']) . '</b><br/>';
                    }

                    if ( isset( $this->field['icon'] ) && ! empty( $this->field['icon'] ) && $this->field['icon'] !== true ) {
                        echo '<p class="redux-info-icon"><i class="' . esc_attr($this->field['icon']) . ' icon-large"></i></p>';
                    }

                    if ( isset( $this->field['raw'] ) && ! empty( $this->field['raw'] ) ) {
                        echo wp_kses_post($this->field['raw']);
                    }

                    if ( ! empty( $this->field['title'] ) || ! empty( $this->field['desc'] ) ) {
                        echo '<p class="redux-info-desc">' . wp_kses_post($this->field['title']) . wp_kses_post($this->field['desc']) . '</p>';
                    }
                }

                echo '</div><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom:0; display:none;"><th style="padding-top:0;"></th><td style="padding-top:0;">';
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
                if ($this->parent->args['dev_mode']) {
                    wp_enqueue_style(
                        'redux-field-info-css',
                        ReduxFramework::$_url . 'inc/fields/info/field_info.css',
                        array(),
                        time(),
                        'all'
                    );
                }
            }
        }
    }