<?php
/**
 * Authorize Application Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

$error        = null;
$new_password = '';

// This is the no-js fallback script.  Generally this will all be handled by `auth-app.js`
if ( isset( $_POST['action'] ) && 'authorize_application_password' === $_POST['action'] ) {
	check_admin_referer( 'authorize_application_password' );

	$success_url = $_POST['success_url'];
	$reject_url  = $_POST['reject_url'];
	$app_name    = $_POST['app_name'];
	$app_id      = $_POST['app_id'];
	$redirect    = '';

	if ( isset( $_POST['reject'] ) ) {
		if ( $reject_url ) {
			$redirect = $reject_url;
		} else {
			$redirect = admin_url();
		}
	} elseif ( isset( $_POST['approve'] ) ) {
		$created = WP_Application_Passwords::create_new_application_password(
			get_current_user_id(),
			array(
				'name'   => $app_name,
				'app_id' => $app_id,
			)
		);

		if ( is_wp_error( $created ) ) {
			$error = $created;
		} else {
			list( $new_password ) = $created;

			if ( $success_url ) {
				$redirect = add_query_arg(
					array(
						'site_url'   => urlencode( site_url() ),
						'user_login' => urlencode( wp_get_current_user()->user_login ),
						'password'   => urlencode( $new_password ),
					),
					$success_url
				);
			}
		}
	}

	if ( $redirect ) {
		// Explicitly not using wp_safe_redirect b/c sends to arbitrary domain.
		wp_redirect( $redirect );
		exit;
	}
}

$title = __( 'Authorize Application' );

$app_name    = ! empty( $_REQUEST['app_name'] ) ? $_REQUEST['app_name'] : '';
$app_id      = ! empty( $_REQUEST['app_id'] ) ? $_REQUEST['app_id'] : '';
$success_url = ! empty( $_REQUEST['success_url'] ) ? $_REQUEST['success_url'] : null;

if ( ! empty( $_REQUEST['reject_url'] ) ) {
	$reject_url = $_REQUEST['reject_url'];
} elseif ( $success_url ) {
	$reject_url = add_query_arg( 'success', 'false', $success_url );
} else {
	$reject_url = null;
}

$user = wp_get_current_user();

$request  = compact( 'app_name', 'app_id', 'success_url', 'reject_url' );
$is_valid = wp_is_authorize_application_password_request_valid( $request, $user );

if ( is_wp_error( $is_valid ) ) {
	wp_die(
		__( 'The Authorize Application request is not allowed.' ) . ' ' . implode( ' ', $is_valid->get_error_messages() ),
		__( 'Cannot Authorize Application' )
	);
}

if ( ! empty( $_SERVER['PHP_AUTH_USER'] ) || ! empty( $_SERVER['PHP_AUTH_PW'] ) ) {
	wp_die(
		__( 'Your website appears to use Basic Authentication, which is not currently compatible with Application Passwords.' ),
		__( 'Cannot Authorize Application' ),
		array(
			'response'  => 501,
			'link_text' => __( 'Go Back' ),
			'link_url'  => $reject_url ? add_query_arg( 'error', 'disabled', $reject_url ) : admin_url(),
		)
	);
}

if ( ! wp_is_application_passwords_available_for_user( $user ) ) {
	if ( wp_is_application_passwords_available() ) {
		$message = __( 'Application passwords are not available for your account. Please contact the site administrator for assistance.' );
	} else {
		$message = __( 'Application passwords are not available.' );
	}

	wp_die(
		$message,
		__( 'Cannot Authorize Application' ),
		array(
			'response'  => 501,
			'link_text' => __( 'Go Back' ),
			'link_url'  => $reject_url ? add_query_arg( 'error', 'disabled', $reject_url ) : admin_url(),
		)
	);
}

wp_enqueue_script( 'auth-app' );
wp_localize_script(
	'auth-app',
	'authApp',
	array(
		'site_url'   => site_url(),
		'user_login' => $user->user_login,
		'success'    => $success_url,
		'reject'     => $reject_url ? $reject_url : admin_url(),
	)
);

require_once ABSPATH . 'wp-admin/admin-header.php';

?>
<div class="wrap">
	<h1><?php echo esc_html( $title ); ?></h1>

	<?php if ( is_wp_error( $error ) ) : ?>
		<div class="notice notice-error"><p><?php echo $error->get_error_message(); ?></p></div>
	<?php endif; ?>

	<div class="card auth-app-card">
		<h2 class="title"><?php __( 'An application would like to connect to your account.' ); ?></h2>
		<?php if ( $app_name ) : ?>
			<p>
				<?php
				printf(
					/* translators: %s: Application name. */
					__( 'Would you like to give the application identifying itself as %s access to your account? You should only do this if you trust the app in question.' ),
					'<strong>' . esc_html( $app_name ) . '</strong>'
				);
				?>
			</p>
		<?php else : ?>
			<p><?php _e( 'Would you like to give this application access to your account? You should only do this if you trust the app in question.' ); ?></p>
		<?php endif; ?>

		<?php
		if ( is_multisite() ) {
			$blogs       = get_blogs_of_user( $user->ID, true );
			$blogs_count = count( $blogs );
			if ( $blogs_count > 1 ) {
				?>
				<p>
					<?php
					printf(
						/* translators: 1: URL to my-sites.php, 2: Number of blogs the user has. */
						_n(
							'This will grant access to <a href="%1$s">the %2$s blog in this installation that you have permissions on</a>.',
							'This will grant access to <a href="%1$s">all %2$s blogs in this installation that you have permissions on</a>.',
							$blogs_count
						),
						admin_url( 'my-sites.php' ),
						number_format_i18n( $blogs_count )
					);
					?>
				</p>
				<?php
			}
		}
		?>

		<?php if ( $new_password ) : ?>
			<div class="notice notice-success notice-alt below-h2">
				<p class="application-password-display">
					<label for="new-application-password-value">
						<?php
						printf(
							/* translators: %s: Application name. */
							esc_html__( 'Your new password for %s is:' ),
							'<strong>' . esc_html( $app_name ) . '</strong>'
						);
						?>
					</label>
					<input id="new-application-password-value" type="text" class="code" readonly="readonly" value="<?php esc_attr( WP_Application_Passwords::chunk_password( $new_password ) ); ?>" />
				</p>
				<p><?php _e( 'Be sure to save this in a safe location. You will not be able to retrieve it.' ); ?></p>
			</div>

			<?php
			/**
			 * Fires in the Authorize Application Password new password section.
			 *
			 * @since 5.6.0
			 *
			 * @param string  $new_password The newly generated application password.
			 * @param array   $request      The array of request data. All arguments are optional and may be empty.
			 * @param WP_User $user         The user authorizing the application.
			 */
			do_action( 'wp_authorize_application_password_form', $request, $user );
			?>
		<?php else : ?>
			<form action="<?php echo esc_url( admin_url( 'authorize-application.php' ) ); ?>" method="post" class="form-wrap">
				<?php wp_nonce_field( 'authorize_application_password' ); ?>
				<input type="hidden" name="action" value="authorize_application_password" />
				<input type="hidden" name="app_id" value="<?php echo esc_attr( $app_id ); ?>" />
				<input type="hidden" name="success_url" value="<?php echo esc_url( $success_url ); ?>" />
				<input type="hidden" name="reject_url" value="<?php echo esc_url( $reject_url ); ?>" />

				<div class="form-field">
					<label for="app_name"><?php _e( 'New Application Password Name' ); ?></label>
					<input type="text" id="app_name" name="app_name" value="<?php echo esc_attr( $app_name ); ?>" placeholder="<?php esc_attr_e( 'WordPress App on My Phone' ); ?>" required />
				</div>

				<?php
				/**
				 * Fires in the Authorize Application Password form before the submit buttons.
				 *
				 * @since 5.6.0
				 *
				 * @param array   $request {
				 *     The array of request data. All arguments are optional and may be empty.
				 *
				 *     @type string $app_name    The suggested name of the application.
				 *     @type string $success_url The url the user will be redirected to after approving the application.
				 *     @type string $reject_url  The url the user will be redirected to after rejecting the application.
				 * }
				 * @param WP_User $user The user authorizing the application.
				 */
				do_action( 'wp_authorize_application_password_form', $request, $user );
				?>

				<?php
				submit_button(
					__( 'Yes, I approve of this connection.' ),
					'primary',
					'approve',
					false,
					array(
						'aria-describedby' => 'description-approve',
					)
				);
				?>
				<p class="description" id="description-approve">
					<?php
					if ( $success_url ) {
						printf(
							/* translators: %s: The URL the user is being redirected to. */
							__( 'You will be sent to %s' ),
							'<strong><kbd>' . esc_html(
								add_query_arg(
									array(
										'site_url'   => site_url(),
										'user_login' => $user->user_login,
										'password'   => '[------]',
									),
									$success_url
								)
							) . '</kbd></strong>'
						);
					} else {
						_e( 'You will be given a password to manually enter into the application in question.' );
					}
					?>
				</p>

				<?php
				submit_button(
					__( 'No, I do not approve of this connection.' ),
					'secondary',
					'reject',
					false,
					array(
						'aria-describedby' => 'description-reject',
					)
				);
				?>
				<p class="description" id="description-reject">
					<?php
					if ( $reject_url ) {
						printf(
							/* translators: %s: The URL the user is being redirected to. */
							__( 'You will be sent to %s' ),
							'<strong><kbd>' . esc_html( $reject_url ) . '</kbd></strong>'
						);
					} else {
						_e( 'You will be returned to the WordPress Dashboard, and no changes will be made.' );
					}
					?>
				</p>
			</form>
		<?php endif; ?>
	</div>
</div>
<?php

require_once ABSPATH . 'wp-admin/admin-footer.php';
