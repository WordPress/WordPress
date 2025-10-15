<?php
/**
 * Security settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Sorry, you are not allowed to manage options for this site.' ) );
}

register_setting(
        'security',
        'wp_mfa_policy',
        array(
                'type'              => 'array',
                'sanitize_callback' => 'wp_sanitize_mfa_policy',
                'default'           => wp_get_mfa_policy(),
        )
);

$title       = __( 'Security Settings' );
$parent_file = 'options-general.php';
$policy      = wp_get_mfa_policy();
$roles       = get_editable_roles();
$window_mins = isset( $policy['email_window'] ) ? (int) floor( $policy['email_window'] / MINUTE_IN_SECONDS ) : 5;
$codes       = implode( "\n", (array) $policy['recovery_codes'] );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>
<?php settings_errors(); ?>

<form method="post" action="options.php">
<?php settings_fields( 'security' ); ?>
<table class="form-table" role="presentation">
        <tr>
                <th scope="row"><?php esc_html_e( 'Require factors for roles' ); ?></th>
                <td>
                        <?php foreach ( $roles as $role_key => $details ) : ?>
                                <label>
                                        <input type="checkbox" name="wp_mfa_policy[required_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, (array) $policy['required_roles'], true ) ); ?> />
                                        <?php echo esc_html( translate_user_role( $details['name'] ) ); ?>
                                </label><br />
                        <?php endforeach; ?>
                        <p class="description"><?php esc_html_e( 'Users in the selected roles must complete multi-factor authentication during login.' ); ?></p>
                </td>
        </tr>
        <tr>
                <th scope="row"><label for="wp_mfa_policy_email_provider"><?php esc_html_e( 'Fallback email delivery' ); ?></label></th>
                <td>
                        <input type="text" class="regular-text" id="wp_mfa_policy_email_provider" name="wp_mfa_policy[email_provider]" value="<?php echo esc_attr( $policy['email_provider'] ); ?>" />
                        <p class="description"><?php esc_html_e( 'Describe the SMTP service or integration that should deliver fallback passcodes.' ); ?></p>
                </td>
        </tr>
        <tr>
                <th scope="row"><label for="wp_mfa_policy_email_rate_limit"><?php esc_html_e( 'Email rate limit' ); ?></label></th>
                <td>
                        <input type="number" class="small-text" min="1" id="wp_mfa_policy_email_rate_limit" name="wp_mfa_policy[email_rate_limit]" value="<?php echo esc_attr( $policy['email_rate_limit'] ); ?>" />
                        <span class="description"><?php esc_html_e( 'Maximum number of passcodes that may be emailed within the window below.' ); ?></span>
                </td>
        </tr>
        <tr>
                <th scope="row"><label for="wp_mfa_policy_email_window"><?php esc_html_e( 'Email rate window (minutes)' ); ?></label></th>
                <td>
                        <input type="number" class="small-text" min="1" id="wp_mfa_policy_email_window" name="wp_mfa_policy[email_window]" value="<?php echo esc_attr( $window_mins ); ?>" />
                        <span class="description"><?php esc_html_e( 'Length of the rolling window used to enforce the rate limit.' ); ?></span>
                </td>
        </tr>
        <tr>
                <th scope="row"><label for="wp_mfa_policy_recovery_codes"><?php esc_html_e( 'Recovery codes' ); ?></label></th>
                <td>
                        <textarea class="large-text code" rows="6" id="wp_mfa_policy_recovery_codes" name="wp_mfa_policy[recovery_codes]" spellcheck="false"><?php echo esc_textarea( $codes ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Enter one recovery code per line. These codes can be provided to users who lose access to their primary factors.' ); ?></p>
                </td>
        </tr>
</table>
<?php submit_button(); ?>
</form>
</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php';
