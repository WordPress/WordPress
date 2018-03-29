<?php

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ReduxFramework_raw' ) ) {
        class ReduxFramework_raw {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since ReduxFramework 3.0.4
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
             * @since ReduxFramework 1.0.0
             */
            function render() {

                if ( ! empty( $this->field['include'] ) && file_exists( $this->field['include'] ) ) {
                    require_once $this->field['include'];
                }

                if ( isset( $this->field['content_path'] ) && ! empty( $this->field['content_path'] ) && file_exists( $this->field['content_path'] ) ) {
                    $this->field['content'] = $this->parent->filesystem->execute( 'get_contents', $this->field['content_path'] );
                }

                if ( ! empty( $this->field['content'] ) && isset( $this->field['content'] ) ) {
                    if ( isset( $this->field['markdown'] ) && $this->field['markdown'] == true && ! empty( $this->field['content'] ) ) {
                        require_once dirname( __FILE__ ) . "/parsedown.php";
                        $Parsedown = new Parsedown();
                        echo $Parsedown->text( $this->field['content'] );
                    } else {
                        echo $this->field['content'];
                    }
                }

                do_action( 'redux-field-raw-' . $this->parent->args['opt_name'] . '-' . $this->field['id'] );

            }
        }
    }