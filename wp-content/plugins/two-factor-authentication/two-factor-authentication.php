<?php
    /**
     * Plugin Name: Two-Factor Authentication
     * Description: Adds two-factor authentication to the WordPress login system.
     * Version: 1.0
     * Author: Leonardo Nascimento
     */

    // Add filter for two-factor authentication code verification
    add_filter( 'authenticate', 'custom_two_factor_auth', 10, 3 );

    function custom_two_factor_auth( $user, $username, $password ) {
        // Check if the user is authenticated correctly with username and password
        if ( is_wp_error( $user ) || ! $user ) {
            return $user;
        }

        // Check if two-factor authentication is enabled for the user
        $two_factor_enabled = get_user_meta( $user->ID, 'two_factor_enabled', true );

        // If two-factor authentication is enabled for the user
        if ( $two_factor_enabled ) {
            // Check if a two-factor authentication code was sent
            if ( isset( $_POST['two_factor_code'] ) && ! empty( $_POST['two_factor_code'] ) ) {
                $code = sanitize_text_field( $_POST['two_factor_code'] );

                // Check if the two-factor authentication code is valid
                if ( $code === get_user_meta( $user->ID, 'two_factor_code', true ) ) {
                    // Two-factor authentication successful
                    return $user;
                } else {
                    // Invalid two-factor authentication code
                    return new WP_Error( 'invalid_two_factor_code', __( 'Invalid two-factor authentication code.', 'text_domain' ) );
                }
            } elseif ( isset( $_POST['two_factor_email'] ) && ! empty( $_POST['two_factor_email'] ) ) {
                // Check if a two-factor authentication email code was sent
                $email_code = sanitize_text_field( $_POST['two_factor_email'] );

                // Check if the two-factor authentication email code is valid
                if ( $email_code === get_user_meta( $user->ID, 'two_factor_email_code', true ) ) {
                    // Two-factor authentication via email successful
                    return $user;
                } else {
                    // Invalid two-factor authentication email code
                    return new WP_Error( 'invalid_two_factor_email_code', __( 'Invalid two-factor authentication email code.', 'text_domain' ) );
                }
            } else {
                // No two-factor authentication code sent
                return new WP_Error( 'two_factor_code_required', __( 'You must enter the two-factor authentication code or the email authentication code.', 'text_domain' ) );
            }
        }

        // Return the authenticated user
        return $user;
    }
?>