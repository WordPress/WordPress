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
 * @subpackage  Field_Gallery
 * @author      Abdullah Almesbahi (cadr-sa)
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_gallery' ) ) {

    /**
     * Main ReduxFramework_gallery class
     *
     * @since       3.0.0
     */
    class ReduxFramework_gallery {

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
            echo '<div class="screenshot">';

            if ( ! empty( $this->value ) ) {
                $ids = explode( ',', $this->value );

                foreach ( $ids as $attachment_id ) {
                    $img = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                    echo '<a class="of-uploaded-image" href="' . $img[0] . '">';
                    echo '<img class="redux-option-image" id="image_' . $this->field['id'] . '_' . $attachment_id . '" src="' . $img[0] . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                }
            }

            echo '</div>';
            echo '<a href="#" onclick="return false;" id="edit-gallery" class="gallery-attachments button button-primary">' . __( 'Add/Edit Gallery', 'redux-framework' ) . '</a> ';
            echo '<a href="#" onclick="return false;" id="clear-gallery" class="gallery-attachments button">' . __( 'Clear Gallery', 'redux-framework' ) . '</a>';
            echo '<input type="hidden" class="gallery_values ' . $this->field['class'] . '" value="' . esc_attr( $this->value ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" />';
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
                wp_enqueue_script( 'media-upload' );
                wp_enqueue_script( 'thickbox' );
                wp_enqueue_style( 'thickbox' );
            }

            wp_enqueue_script(
                'redux-field-gallery-js',
                ReduxFramework::$_url . 'inc/fields/gallery/field_gallery' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );
        }
    }
}