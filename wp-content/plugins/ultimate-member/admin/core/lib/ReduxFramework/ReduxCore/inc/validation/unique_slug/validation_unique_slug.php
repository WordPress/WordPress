<?php

    if ( ! class_exists( 'Redux_Validation_unique_slug' ) ) {
        class Redux_Validation_unique_slug {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since ReduxFramework 1.0.0
             */
            function __construct( $parent, $field, $value, $current ) {

                $this->parent                    = $parent;
                $this->field                     = $field;
                $this->field['msg']              = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __( 'That URL slug is in use, please choose another. <code>%s</code> is open for use.', 'redux-framework' );
                $this->field['flush_permalinks'] = ( isset( $this->field['flush_permalinks'] ) ) ? $this->field['flush_permalinks'] : false;
                $this->value                     = $value;
                $this->current                   = $current;
                $this->validate();

            } //function

            function validate() {

                global $wpdb, $wp_rewrite;

                $slug = $this->value;

                $feeds = $wp_rewrite->feeds;
                if ( ! is_array( $feeds ) ) {
                    $feeds = array();
                }

                // Post slugs must be unique across all posts.
                $check_sql       = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s LIMIT 1";
                $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug ) );

                /**
                 * Filter whether the post slug would be bad as a flat slug.
                 *
                 * @since 3.1.0
                 *
                 * @param bool   $bad_slug  Whether the post slug would be bad as a flat slug.
                 * @param string $slug      The post slug.
                 * @param string $post_type Post type.
                 */
                if ( $post_name_check || in_array( $slug, $feeds ) || apply_filters( 'wp_unique_post_slug_is_bad_attachment_slug', false, $slug ) ) {
                    $suffix = 2;
                    do {
                        $alt_post_name   = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
                        $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name ) );
                        $suffix ++;
                    } while ( $post_name_check );
                    $slug               = $alt_post_name;
                    $this->value        = ( isset( $this->current ) ) ? $this->current : '';
                    $this->field['msg'] = sprintf( $this->field['msg'], $slug );
                    $this->error        = $this->field;
                } else if ( isset( $this->field['flush_permalinks'] ) && $this->field['flush_permalinks'] == true ) {
                    add_action( 'init', array( $this, 'flush_permalinks' ), 99 );
                }
                
            } //function

            function flush_permalinks() {
                flush_rewrite_rules();
            }
        } //class
    }
