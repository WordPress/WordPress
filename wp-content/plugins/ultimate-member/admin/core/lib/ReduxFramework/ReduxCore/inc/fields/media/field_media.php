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
 * @subpackage  Field_Media
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @author      Kevin Provance (kprovance)
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_media' ) ) {

    /**
     * Main ReduxFramework_media class
     *
     * @since       1.0.0
     */
    class ReduxFramework_media {

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
                'id'        => '',
                'url'       => '',
                'width'     => '',
                'height'    => '',
                'thumbnail' => '',
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            if (isset($this->field['mode']) && $this->field['mode'] == false) {
                $this->field['mode'] = 0;
            }

            if ( ! isset( $this->field['mode'] ) ) {
                $this->field['mode'] = "image";
            }

            if (!isset($this->field['library_filter'])) {
                $libFilter = '';
            } else {
                if (!is_array($this->field['library_filter'])) {
                    $this->field['library_filter'] = array($this->field['library_filter']);
                }

                $mimeTypes = get_allowed_mime_types();

                $libArray = $this->field['library_filter'];

                $jsonArr = array();

                // Enum mime types
                foreach ($mimeTypes as $ext => $type) {
                    if (strpos($ext,'|')) {
                        $expArr = explode('|', $ext);

                        foreach($expArr as $ext){
                            if (in_array($ext, $libArray )) {
                                $jsonArr[$ext] = $type;
                            }
                        }
                    } elseif (in_array($ext, $libArray )) {
                        $jsonArr[$ext] = $type;
                    }

                }

                $libFilter = urlencode(json_encode($jsonArr));
            }

            if ( empty( $this->value ) && ! empty( $this->field['default'] ) ) { // If there are standard values and value is empty
                if ( is_array( $this->field['default'] ) ) {
                    if ( ! empty( $this->field['default']['id'] ) ) {
                        $this->value['id'] = $this->field['default']['id'];
                    }

                    if ( ! empty( $this->field['default']['url'] ) ) {
                        $this->value['url'] = $this->field['default']['url'];
                    }
                } else {
                    if ( is_numeric( $this->field['default'] ) ) { // Check if it's an attachment ID
                        $this->value['id'] = $this->field['default'];
                    } else { // Must be a URL
                        $this->value['url'] = $this->field['default'];
                    }
                }
            }


            if ( empty( $this->value['url'] ) && ! empty( $this->value['id'] ) ) {
                $img                   = wp_get_attachment_image_src( $this->value['id'], 'full' );
                $this->value['url']    = $img[0];
                $this->value['width']  = $img[1];
                $this->value['height'] = $img[2];
            }

            $hide = 'hide ';

            if ( ( isset( $this->field['preview'] ) && $this->field['preview'] === false ) ) {
                $this->field['class'] .= " noPreview";
            }

            if ( ( ! empty( $this->field['url'] ) && $this->field['url'] === true ) || isset( $this->field['preview'] ) && $this->field['preview'] === false ) {
                $hide = '';
            }

            $placeholder = isset( $this->field['placeholder'] ) ? $this->field['placeholder'] : __( 'No media selected', 'redux-framework' );

            $readOnly = ' readonly="readonly"';
            if ( isset( $this->field['readonly'] ) && $this->field['readonly'] === false ) {
                $readOnly = '';
            }

            echo '<input placeholder="' . $placeholder . '" type="text" class="' . $hide . 'upload large-text ' . $this->field['class'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[url]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][url]" value="' . $this->value['url'] . '"' . $readOnly . '/>';
            echo '<input type="hidden" class="data" data-mode="' . $this->field['mode'] . '" />';
            echo '<input type="hidden" class="library-filter" data-lib-filter="' . $libFilter . '" />';
            echo '<input type="hidden" class="upload-id ' . $this->field['class'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[id]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][id]" value="' . $this->value['id'] . '" />';
            echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . $this->field['name_suffix'] . '[height]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][height]" value="' . $this->value['height'] . '" />';
            echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . $this->field['name_suffix'] . '[width]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][width]" value="' . $this->value['width'] . '" />';
            echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . $this->field['name_suffix'] . '[thumbnail]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][thumbnail]" value="' . $this->value['thumbnail'] . '" />';

            //Preview
            $hide = '';

            if ( ( isset( $this->field['preview'] ) && $this->field['preview'] === false ) || empty( $this->value['url'] ) ) {
                $hide = 'hide ';
            }

            if ( empty( $this->value['thumbnail'] ) && ! empty( $this->value['url'] ) ) { // Just in case
                if ( ! empty( $this->value['id'] ) ) {
                    $image                    = wp_get_attachment_image_src( $this->value['id'], array(
                            150,
                            150
                        ) );

                    if (empty($image[0]) || $image[0] == '') {
                        $this->value['thumbnail'] = $this->value['url'];
                    } else {
                        $this->value['thumbnail'] = $image[0];
                    }
                } else {
                    $this->value['thumbnail'] = $this->value['url'];
                }
            }

            echo '<div class="' . $hide . 'screenshot">';
            echo '<a class="of-uploaded-image" href="' . $this->value['url'] . '" target="_blank">';
            echo '<img class="redux-option-image" id="image_' . $this->field['id'] . '" src="' . $this->value['thumbnail'] . '" alt="" target="_blank" rel="external" />';
            echo '</a>';
            echo '</div>';

            //Upload controls DIV
            echo '<div class="upload_button_div">';

            //If the user has WP3.5+ show upload/remove button
            echo '<span class="button media_upload_button" id="' . $this->field['id'] . '-media">' . __( 'Upload', 'redux-framework' ) . '</span>';

            $hide = '';
            if ( empty( $this->value['url'] ) || $this->value['url'] == '' ) {
                $hide = ' hide';
            }

            echo '<span class="button remove-image' . $hide . '" id="reset_' . $this->field['id'] . '" rel="' . $this->field['id'] . '">' . __( 'Remove', 'redux-framework' ) . '</span>';

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
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            } else {
                wp_enqueue_script( 'media-upload' );
            }
            
            wp_enqueue_script(
                'redux-field-media-js',
                ReduxFramework::$_url . 'assets/js/media/media' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style('redux-field-media-css');
            }
        }
    }
}