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
     * @author      Kevin Provance (kprovance)
     * @version     3.5.4
     */

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

// Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_options_object' ) ) {

        /**
         * Main ReduxFramework_options_object class
         *
         * @since       1.0.0
         */
        class ReduxFramework_options_object {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            function __construct( $field = array(), $value = '', $parent ) {
                
                $this->parent   = $parent;
                $this->field    = $field;
                $this->value    = $value;
                $this->is_field = $this->parent->extensions['options_object']->is_field;

                $this->extension_dir = ReduxFramework::$_dir . 'inc/extensions/options_object/';
                $this->extension_url = ReduxFramework::$_url . 'inc/extensions/options_object/';

                // Set default args for this field to avoid bad indexes. Change this to anything you use.
                $defaults    = array(
                    'options'          => array(),
                    'stylesheet'       => '',
                    'output'           => true,
                    'enqueue'          => true,
                    'enqueue_frontend' => true
                );
                $this->field = wp_parse_args( $this->field, $defaults );

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
                if ( version_compare( phpversion(), "5.3.0", ">=" ) ) {
                    $json = json_encode( $this->parent->options, true );
                } else {
                    $json = json_encode( $this->parent->options );
                }
                
                $defaults = array(
                    'full_width' => true,
                    'overflow'   => 'inherit',
                );

                $this->field = wp_parse_args( $this->field, $defaults );
                
                if ( $this->is_field ) {
                    $fullWidth = $this->field['full_width'];
                }

                $bDoClose = false;

                $id = $this->parent->args['opt_name'] . '-' . $this->field['id'];
                
                if ( ! $this->is_field || ( $this->is_field && false == $fullWidth ) ) { ?>
                    <style>#<?php echo esc_html($id); ?> {padding: 0;}</style>
                    </td></tr></table>
                    <table id="<?php echo esc_attr($id); ?>" class="form-table no-border redux-group-table redux-raw-table" style=" overflow: <?php esc_attr($this->field['overflow']); ?>;">
                    <tbody><tr><td>
<?php
                    $bDoClose = true;
                }
?>                
                <fieldset id="<?php echo esc_attr($id); ?>" class="redux-field redux-container-<?php echo esc_attr($this->field['type']) . ' ' . esc_attr($this->field['class']); ?>" data-id="<?php echo esc_attr($this->field['id']); ?>">
                    <h3><?php esc_html_e( 'Options Object', 'redux-framework' ); ?></h3>
                    <div id="redux-object-browser"></div>
                    <div id="redux-object-json" class="hide"><?php echo $json; ?></div>
                    <a href="#" id="consolePrintObject" class="button"><?php esc_html_e( 'Show Object in Javascript Console Object', 'redux-framework' ); ?></a>
                </div>
                </fieldset>
<?php
                if ( true == $bDoClose ) { ?>
                    </td></tr></table>
                    <table class="form-table no-border" style="margin-top: 0;">
                        <tbody>
                        <tr style="border-bottom: 0;">
                            <th></th>
                            <td>
<?php
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
            public function enqueue() {

                wp_enqueue_script(
                    'redux-options-object',
                    $this->extension_url . 'options_object/field_options_object' . Redux_Functions::isMin() . '.js',
                    array( 'jquery' ),
                    ReduxFramework_extension_options_object::$version,
                    true
                );

                wp_enqueue_style(
                    'redux-options-object',
                    $this->extension_url . 'options_object/field_options_object.css',
                    array(),
                    time(),
                    'all'
                );
            }

            /**
             * Output Function.
             * Used to enqueue to the front-end
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function output() {

                if ( $this->field['enqueue_frontend'] ) {

                }
            }
        }
    }
