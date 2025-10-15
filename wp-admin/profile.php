<?php
/**
 * User Profile Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * This is a profile page.
 *
 * @since 2.5.0
 * @var bool
 */
define( 'IS_PROFILE_PAGE', true );

add_action( 'show_user_profile', 'wp_profile_render_mfa_settings' );
add_action( 'edit_user_profile', 'wp_profile_render_mfa_settings' );
add_action( 'personal_options_update', 'wp_profile_handle_mfa_update' );
add_action( 'edit_user_profile_update', 'wp_profile_handle_mfa_update' );
add_action( 'admin_notices', 'wp_profile_mfa_admin_notices' );

/**
 * Renders the multi-factor authentication controls on the profile page.
 *
 * @since 6.7.0
 *
 * @param WP_User $profile_user User whose profile is being edited.
 */
function wp_profile_render_mfa_settings( $profile_user ) {
        if ( ! current_user_can( 'edit_user', $profile_user->ID ) ) {
                return;
        }

        $factors        = wp_get_user_auth_factors( $profile_user->ID );
        $pending_secret = isset( $_REQUEST['mfa_totp_secret'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['mfa_totp_secret'] ) ) : wp_generate_totp_secret();
        $has_email      = false;
        $factor_labels  = array(
                'totp'     => __( 'Authenticator app' ),
                'email'    => __( 'Email passcodes' ),
                'webauthn' => __( 'Security key' ),
        );

        foreach ( $factors as $factor ) {
                if ( 'email' === $factor['type'] ) {
                        $has_email = true;
                }
        }

        ?>
        <h2><?php esc_html_e( 'Multi-factor authentication' ); ?></h2>
        <p><?php esc_html_e( 'Add additional verification factors to protect this account.' ); ?></p>
        <table class="form-table" role="presentation">
                <tr>
                        <th scope="row"><?php esc_html_e( 'Enrolled factors' ); ?></th>
                        <td>
                                <?php if ( ! empty( $factors ) ) : ?>
                                        <ul>
                                                <?php foreach ( $factors as $factor ) : ?>
                                                        <li>
                                                                <label>
                                                                        <input type="checkbox" name="mfa_remove[]" value="<?php echo esc_attr( $factor['id'] ); ?>" />
                                                                        <strong><?php echo esc_html( $factor['label'] ? $factor['label'] : ( $factor_labels[ $factor['type'] ] ?? $factor['type'] ) ); ?></strong>
                                                                        <span class="description">
                                                                                <?php
                                                                                if ( ! empty( $factor['added'] ) ) {
                                                                                        printf( esc_html__( 'Added on %s.' ), esc_html( date_i18n( get_option( 'date_format' ), $factor['added'] ) ) );
                                                                                }
                                                                                ?>
                                                                        </span>
                                                                </label>
                                                        </li>
                                                <?php endforeach; ?>
                                        </ul>
                                <?php else : ?>
                                        <p class="description"><?php esc_html_e( 'No additional factors are configured for this account.' ); ?></p>
                                <?php endif; ?>
                        </td>
                </tr>
                <tr>
                        <th scope="row"><label for="mfa_totp_secret"><?php esc_html_e( 'Authenticator application' ); ?></label></th>
                        <td>
                                <p class="description"><?php esc_html_e( 'Scan this secret with your authenticator app and enter the current verification code to enroll.' ); ?></p>
                                <input type="text" id="mfa_totp_secret" name="mfa_totp_secret" class="regular-text" value="<?php echo esc_attr( $pending_secret ); ?>" readonly />
                                <p>
                                        <label for="mfa_totp_code"><?php esc_html_e( 'Verification code' ); ?></label>
                                        <input type="text" name="mfa_totp_code" id="mfa_totp_code" class="regular-text" autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]*" />
                                </p>
                                <p>
                                        <button type="submit" class="button button-secondary" name="mfa_totp_submit" value="1"><?php esc_html_e( 'Add authenticator' ); ?></button>
                                </p>
                                <input type="hidden" name="profile_mfa_section" value="1" />
                        </td>
                </tr>
                <tr>
                        <th scope="row"><?php esc_html_e( 'Email verification' ); ?></th>
                        <td>
                                <?php if ( $has_email ) : ?>
                                        <p class="description"><?php esc_html_e( 'Email verification codes are enabled.' ); ?></p>
                                <?php else : ?>
                                        <p class="description"><?php esc_html_e( 'Send one-time passcodes to the user email address as a fallback.' ); ?></p>
                                        <button type="submit" class="button" name="mfa_enable_email" value="1"><?php esc_html_e( 'Enable email verification' ); ?></button>
                                <?php endif; ?>
                        </td>
                </tr>
                <?php if ( ! empty( $factors ) ) : ?>
                <tr>
                        <th scope="row"><?php esc_html_e( 'Remove factors' ); ?></th>
                        <td>
                                <p class="description"><?php esc_html_e( 'Select enrolled factors above and remove them if they are no longer needed.' ); ?></p>
                                <button type="submit" class="button button-link-delete" name="mfa_remove_action" value="1"><?php esc_html_e( 'Remove selected factors' ); ?></button>
                        </td>
                </tr>
                <?php endif; ?>
        </table>
        <?php
}

/**
 * Handles multi-factor form submissions from the profile page.
 *
 * @since 6.7.0
 *
 * @param int $user_id User ID being updated.
 */
function wp_profile_handle_mfa_update( $user_id ) {
        if ( empty( $_POST['profile_mfa_section'] ) ) {
                return;
        }

        if ( ! current_user_can( 'edit_user', $user_id ) ) {
                return;
        }

        check_admin_referer( 'update-user_' . $user_id );

        $notices = array();

        if ( isset( $_POST['mfa_enable_email'] ) ) {
                $result = wp_profile_enable_email_factor( $user_id );
                $notices[] = $result;
        }

        if ( isset( $_POST['mfa_totp_submit'] ) ) {
                $result = wp_profile_enroll_totp_factor( $user_id );
                $notices[] = $result;
        }

        if ( isset( $_POST['mfa_remove_action'] ) && ! empty( $_POST['mfa_remove'] ) ) {
                $result = wp_profile_remove_selected_factors( $user_id, (array) $_POST['mfa_remove'] );
                $notices[] = $result;
        }

        $notices = array_filter( $notices );

        if ( ! empty( $notices ) ) {
                set_transient( 'wp_mfa_notice_' . get_current_user_id(), $notices, MINUTE_IN_SECONDS );
        }
}

/**
 * Registers an email MFA factor through the profile UI.
 *
 * @since 6.7.0
 *
 * @param int $user_id User ID.
 * @return array Notice data.
 */
function wp_profile_enable_email_factor( $user_id ) {
        $user = get_userdata( $user_id );

        if ( ! $user || ! is_email( $user->user_email ) ) {
                return array(
                        'type'    => 'notice-error',
                        'message' => __( 'A valid email address is required to enable email verification.' ),
                );
        }

        $factors = wp_get_user_auth_factors( $user_id );

        foreach ( $factors as $factor ) {
                if ( 'email' === $factor['type'] ) {
                        return array(
                                'type'    => 'notice-warning',
                                'message' => __( 'Email verification is already enabled for this account.' ),
                        );
                }
        }

        $factor_id = 'email-' . strtolower( wp_generate_password( 6, false, false ) );
        $factor    = array(
                'id'    => $factor_id,
                'type'  => 'email',
                'label' => __( 'Email passcodes' ),
                'added' => time(),
        );

        $factor = apply_filters( 'wp_rest_mfa_email_factor', $factor, $user, null );

        wp_register_user_auth_factor( $user_id, $factor );

        return array(
                'type'    => 'notice-success',
                'message' => __( 'Email verification has been enabled.' ),
        );
}

/**
 * Enrolls a TOTP factor from the profile UI.
 *
 * @since 6.7.0
 *
 * @param int $user_id User ID.
 * @return array Notice data.
 */
function wp_profile_enroll_totp_factor( $user_id ) {
        $user   = get_userdata( $user_id );
        if ( ! $user ) {
                return array(
                        'type'    => 'notice-error',
                        'message' => __( 'Unable to locate the user for enrollment.' ),
                );
        }
        $secret = isset( $_POST['mfa_totp_secret'] ) ? sanitize_text_field( wp_unslash( $_POST['mfa_totp_secret'] ) ) : '';
        $code   = isset( $_POST['mfa_totp_code'] ) ? sanitize_text_field( wp_unslash( $_POST['mfa_totp_code'] ) ) : '';

        if ( '' === $secret || '' === $code ) {
                        return array(
                                'type'    => 'notice-error',
                                'message' => __( 'You must provide the authenticator secret and current verification code.' ),
                        );
        }

        $secret  = strtoupper( preg_replace( '/[^A-Z2-7]/', '', $secret ) );
        $decoded = wp_mfa_base32_decode( $secret );

        if ( '' === $decoded ) {
                return array(
                        'type'    => 'notice-error',
                        'message' => __( 'The supplied authenticator secret is invalid.' ),
                );
        }

        $valid = false;

        for ( $offset = -1; $offset <= 1; $offset++ ) {
                $expected = wp_generate_totp_from_secret( $decoded, time() + ( $offset * 30 ) );

                if ( hash_equals( $expected, $code ) ) {
                        $valid = true;
                        break;
                }
        }

        if ( ! $valid ) {
                return array(
                        'type'    => 'notice-error',
                        'message' => __( 'The verification code did not match. Try again.' ),
                );
        }

        $factor_id = 'totp-' . strtolower( wp_generate_password( 8, false, false ) );
        $factor    = array(
                'id'     => $factor_id,
                'type'   => 'totp',
                'label'  => __( 'Authenticator app' ),
                'added'  => time(),
                'secret' => wp_encrypt_user_mfa_secret( $secret, $factor_id ),
        );

        $factor = apply_filters( 'wp_rest_mfa_totp_factor', $factor, $user, null );

        wp_register_user_auth_factor( $user_id, $factor );

        return array(
                'type'    => 'notice-success',
                'message' => __( 'The authenticator app has been added.' ),
        );
}

/**
 * Removes selected MFA factors from a user profile.
 *
 * @since 6.7.0
 *
 * @param int   $user_id User ID.
 * @param array $factor_ids Factor identifiers.
 * @return array Notice data.
 */
function wp_profile_remove_selected_factors( $user_id, $factor_ids ) {
        $removed = 0;
        $factors = wp_get_user_auth_factors( $user_id );

        foreach ( $factor_ids as $factor_id ) {
                $factor_id = sanitize_key( $factor_id );

                if ( isset( $factors[ $factor_id ] ) ) {
                        wp_delete_user_auth_factor( $user_id, $factor_id );
                        unset( $factors[ $factor_id ] );
                        $removed++;
                }
        }

        if ( $removed > 0 ) {
                return array(
                        'type'    => 'notice-success',
                        'message' => sprintf( _n( '%d factor was removed.', '%d factors were removed.', $removed ), $removed ),
                );
        }

        return array(
                'type'    => 'notice-warning',
                'message' => __( 'No matching factors were selected for removal.' ),
        );
}

/**
 * Displays MFA admin notices for the profile screen.
 *
 * @since 6.7.0
 */
function wp_profile_mfa_admin_notices() {
        if ( ! function_exists( 'get_current_screen' ) ) {
                return;
        }

        $screen = get_current_screen();

        if ( ! $screen || ! in_array( $screen->id, array( 'profile', 'user-edit' ), true ) ) {
                return;
        }

        $notices = get_transient( 'wp_mfa_notice_' . get_current_user_id() );

        if ( empty( $notices ) ) {
                return;
        }

        delete_transient( 'wp_mfa_notice_' . get_current_user_id() );

        foreach ( $notices as $notice ) {
                $class   = isset( $notice['type'] ) ? $notice['type'] : 'notice-info';
                $message = isset( $notice['message'] ) ? $notice['message'] : '';

                if ( '' === $message ) {
                        continue;
                }

                printf( '<div class="notice %1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        }
}

/** Load User Editing Page */
require_once __DIR__ . '/user-edit.php';
