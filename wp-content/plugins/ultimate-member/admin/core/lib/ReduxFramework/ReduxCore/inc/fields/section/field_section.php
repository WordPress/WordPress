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
     * @subpackage  Field_Section
     * @author      Tobias Karnetze (athoss.de)
     * @version     1.0.0
     */

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

// Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_section' ) ) {

        /**
         * Main ReduxFramework_heading class
         *
         * @since       1.0.0
         */
        class ReduxFramework_section {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since         1.0.0
             * @access        public
             * @return        void
             */
            public function __construct( $field = array(), $value = '', $parent ) {
                $this->parent = $parent;
                $this->field  = $field;
                $this->value  = $value;
            }

            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since         1.0.0
             * @access        public
             * @return        void
             */
            public function render() {

                // No errors please
                $defaults    = array(
                    'indent'   => '',
                    'style'    => '',
                    'class'    => '',
                    'title'    => '',
                    'subtitle' => '',
                );
                $this->field = wp_parse_args( $this->field, $defaults );

                $guid = uniqid();

                $add_class = '';
                if ( isset( $this->field['indent'] ) &&  true === $this->field['indent'] ) {
                    $add_class = ' form-table-section-indented';
                } elseif( !isset( $this->field['indent'] ) || ( isset( $this->field['indent'] ) && false !== $this->field['indent'] ) ) {
                    $add_class = " hide";
                }

                echo '<input type="hidden" id="' . esc_attr($this->field['id']) . '-marker"></td></tr></table>';

                echo '<div id="section-' . esc_attr($this->field['id']) . '" class="redux-section-field redux-field ' . esc_attr($this->field['style']) . ' ' . esc_attr($this->field['class']) . ' ">';

                if ( ! empty( $this->field['title'] ) ) {
                    echo '<h3>' . esc_html($this->field['title']) . '</h3>';
                }

                if ( ! empty( $this->field['subtitle'] ) ) {
                    echo '<div class="redux-section-desc">' . esc_html($this->field['subtitle']) . '</div>';
                }

                echo '</div><table id="section-table-' . esc_attr($this->field['id']) . '" data-id="' . esc_attr($this->field['id']) . '" class="form-table form-table-section no-border' . esc_attr($add_class) . '"><tbody><tr><th></th><td id="' . esc_attr($guid) . '">';

                // delete the tr afterwards
                ?>
                <script type="text/javascript">
                    jQuery( document ).ready(
                        function() {
                            jQuery( '#<?php echo esc_js($this->field['id']); ?>-marker' ).parents( 'tr:first' ).css( {display: 'none'} ).prev('tr' ).css('border-bottom','none');;
                            var group = jQuery( '#<?php echo esc_js($this->field['id']); ?>-marker' ).parents( '.redux-group-tab:first' );
                            if ( !group.hasClass( 'sectionsChecked' ) ) {
                                group.addClass( 'sectionsChecked' );
                                var test = group.find( '.redux-section-indent-start h3' );
                                jQuery.each(
                                    test, function( key, value ) {
                                        jQuery( value ).css( 'margin-top', '20px' )
                                    }
                                );
                                if ( group.find( 'h3:first' ).css( 'margin-top' ) == "20px" ) {
                                    group.find( 'h3:first' ).css( 'margin-top', '0' );
                                }
                            }
                        }
                    );
                </script>
            <?php

            }

            public function enqueue() {
                if ( $this->parent->args['dev_mode'] ) {
                    wp_enqueue_style(
                        'redux-field-section-css',
                        ReduxFramework::$_url . 'inc/fields/section/field_section.css',
                        array(),
                        time(),
                        'all'
                    );
                }
            }
        }
    }