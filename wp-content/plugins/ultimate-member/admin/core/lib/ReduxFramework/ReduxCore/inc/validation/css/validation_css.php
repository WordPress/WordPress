<?php

    if ( ! class_exists( 'Redux_Validation_css' ) ) {
        class Redux_Validation_css {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since ReduxFramework 1.0.0
             */
            function __construct( $parent, $field, $value, $current ) {

                $this->parent  = $parent;
                $this->field   = $field;
                $this->value   = $value;
                $this->current = $current;

                $this->validate();
            }

            //function

            /**
             * Field Render Function.
             * Takes the vars and validates them
             *
             * @since ReduxFramework 3.0.0
             */
            function validate() {

                $data = $this->value;

                $data = wp_filter_nohtml_kses( $data );
                $data = str_replace( '&gt;', '>', $data );
                $data = stripslashes($data);

                if ($data != $this->value) {
                    $this->field['msg'] = __( 'Unsafe strings were found in your CSS and have been filtered out.', 'redux-framework' );
                    $this->warning = $this->field;
                }

                $this->value = $data;

            } //function
        } //class
    }