<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_sortable' ) ) {
    class ReduxFramework_sortable {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since Redux_Options 2.0.1
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
         * @since Redux_Options 2.0.1
         */
        function render() {
            if ( empty( $this->field['mode'] ) ) {
                $this->field['mode'] = "text";
            }

            if ( $this->field['mode'] != "checkbox" && $this->field['mode'] != "text" ) {
                $this->field['mode'] = "text";
            }

            $class   = ( isset( $this->field['class'] ) ) ? $this->field['class'] : '';
            $options = $this->field['options'];

            // This is to weed out missing options that might be in the default
            // Why?  Who knows.  Call it a dummy check.
            if ( ! empty( $this->value ) ) {
                foreach ( $this->value as $k => $v ) {
                    if ( ! isset( $options[ $k ] ) ) {
                        unset( $this->value[ $k ] );
                    }
                }
            }

            $noSort = false;
            foreach ( $options as $k => $v ) {
                if ( ! isset( $this->value[ $k ] ) ) {

                    // A save has previously been done.
                    if ( is_array( $this->value ) && array_key_exists( $k, $this->value ) ) {
                        $this->value[ $k ] = $v;

                        // Missing database entry, meaning no save has yet been done.
                    } else {
                        $noSort            = true;
                        $this->value[ $k ] = '';
                    }
                }
            }

            // If missing database entries are found, it means no save has been done
            // and therefore no sort should be done.  Set the default array in the same
            // order as the options array.  Why?  The sort order is based on the
            // saved default array.  If entries are missing, the sort is messed up.
            // - kp
            if ( true == $noSort ) {
                $dummyArr = array();

                foreach ( $options as $k => $v ) {
                    $dummyArr[ $k ] = $this->value[ $k ];
                }
                unset( $this->value );
                $this->value = $dummyArr;
                unset( $dummyArr );
            }

            $use_labels = false;
            $label_class = '';
            if ( $this->field['mode'] != "checkbox" ) {
                if ( ( isset( $this->field['label'] ) && $this->field['label'] == true ) ) {
                    $use_labels = true;
                    $label_class = ' labeled';
                }
            }
            
            echo '<ul id="' . $this->field['id'] . '-list" class="redux-sortable ' . $class . ' ' . $label_class . '">';


            foreach ( $this->value as $k => $nicename ) {
                echo '<li>';

                $checked = "";
                $name    = 'name="' . $this->field['name'] . $this->field['name_suffix'] . '[' . $k . ']' . '" ';
                if ( $this->field['mode'] == "checkbox" ) {
                    $value_display = $this->value[ $k ];

                    if ( ! empty( $this->value[ $k ] ) ) {
                        $checked = 'checked="checked" ';
                    }
                    $class .= " checkbox_sortable";
                    $name = "";
                    echo '<input type="hidden" name="' . $this->field['name'] . $this->field['name_suffix'] . '[' . $k . ']' . '" id="' . $this->field['id'] . '-' . $k . '-hidden" value="' . $value_display . '" />';

                    echo '<div class="checkbox-container">';
                } else {
                    $value_display = isset( $this->value[ $k ] ) ? $this->value[ $k ] : '';
                    $nicename = $this->field['options'][$k];

                }

                if ($this->field['mode'] != "checkbox") {
                    if ($use_labels) {
                        echo '<label class="bugger" for="' . $this->field['id'] . '[' . $k . ']"><strong>' . $k . '</strong></label>';
                        echo "<br />";
                    }
                }

                echo '<input rel="' . $this->field['id'] . '-' . $k . '-hidden" class="' . $class . '" ' . $checked . 'type="' . $this->field['mode'] . '" ' . $name . 'id="' . $this->field['id'] . '[' . $k . ']" value="' . esc_attr( $value_display ) . '" placeholder="' . $nicename . '" />';

                echo '<span class="compact drag"><i class="el el-move icon-large"></i></span>';
                //if ( ( isset( $this->field['label'] ) && $this->field['label'] == true ) ) {
                if ($this->field['mode'] == "checkbox") {
                    if ( $this->field['mode'] != "checkbox" ) {
                        //echo "<br />";
                        //echo '<label for="' . $this->field['id'] . '[' . $k . ']"><strong>' . $k . '</strong></label>';
                    } else {
                        echo '<label for="' . $this->field['id'] . '[' . $k . ']"><strong>' .  $options[$k] . '</strong></label>';
                    }
                }
                if ( $this->field['mode'] == "checkbox" ) {
                    echo '</div>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }

        function enqueue() {
            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-sortable-css',
                    ReduxFramework::$_url . 'inc/fields/sortable/field_sortable.css',
                    array(),
                    time(),
                    'all'
                );
            }

            wp_enqueue_script(
                'redux-field-sortable-js',
                ReduxFramework::$_url . 'inc/fields/sortable/field_sortable' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js', 'jquery-ui-sortable' ),
                time(),
                true
            );
        }
    }
}
