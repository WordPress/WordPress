<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

wp_reset_vars( array( 'action', 'user_id', 'wp_http_referer' ) );

$user_id      = (int) $user_id;
$current_user = wp_get_current_user();

if ( ! defined( 'IS_PROFILE_PAGE' ) ) {
	define( 'IS_PROFILE_PAGE', ( $user_id === $current_user->ID ) );
}

if ( ! $user_id && IS_PROFILE_PAGE ) {
	$user_id = $current_user->ID;
} elseif ( ! $user_id && ! IS_PROFILE_PAGE ) {
	wp_die( __( 'Invalid user ID.' ) );
} elseif ( ! get_userdata( $user_id ) ) {
	wp_die( __( 'Invalid user ID.' ) );
}

wp_enqueue_script( 'user-profile' );

if ( wp_is_application_passwords_available_for_user( $user_id ) ) {
	wp_enqueue_script( 'application-passwords' );
}

if ( IS_PROFILE_PAGE ) {
	// Used in the HTML title tag.
	$title = __( 'Profile' );
} else {
	// Used in the HTML title tag.
	/* translators: %s: User's display name. */
	$title = __( 'Edit User %s' );
}

if ( current_user_can( 'edit_users' ) && ! IS_PROFILE_PAGE ) {
	$submenu_file = 'users.php';
} else {
	$submenu_file = 'profile.php';
}

if ( current_user_can( 'edit_users' ) && ! is_user_admin() ) {
	$parent_file = 'users.php';
} else {
	$parent_file = 'profile.php';
}

$profile_help = '<p>' . __( 'Your profile contains information about you (your &#8220;account&#8221;) as well as some personal options related to using WordPress.' ) . '</p>' .
	'<p>' . __( 'You can change your password, turn on keyboard shortcuts, change the color scheme of your WordPress administration screens, and turn off the WYSIWYG (Visual) editor, among other things. You can hide the Toolbar (formerly called the Admin Bar) from the front end of your site, however it cannot be disabled on the admin screens.' ) . '</p>' .
	'<p>' . __( 'You can select the language you wish to use while using the WordPress administration screen without affecting the language site visitors see.' ) . '</p>' .
	'<p>' . __( 'Your username cannot be changed, but you can use other fields to enter your real name or a nickname, and change which name to display on your posts.' ) . '</p>' .
	'<p>' . __( 'You can log out of other devices, such as your phone or a public computer, by clicking the Log Out Everywhere Else button.' ) . '</p>' .
	'<p>' . __( 'Required fields are indicated; the rest are optional. Profile information will only be displayed if your theme is set up to do so.' ) . '</p>' .
	'<p>' . __( 'Remember to click the Update Profile button when you are finished.' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => $profile_help,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/users-your-profile-screen/">Documentation on User Profiles</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

$wp_http_referer = remove_query_arg( array( 'update', 'delete_count', 'user_id' ), $wp_http_referer );

$user_can_edit = current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );

/**
 * Filters whether to allow administrators on Multisite to edit every user.
 *
 * Enabling the user editing form via this filter also hinges on the user holding
 * the 'manage_network_users' cap, and the logged-in user not matching the user
 * profile open for editing.
 *
 * The filter was introduced to replace the EDIT_ANY_USER constant.
 *
 * @since 3.0.0
 *
 * @param bool $allow Whether to allow editing of any user. Default true.
 */
if ( is_multisite()
	&& ! current_user_can( 'manage_network_users' )
	&& $user_id !== $current_user->ID
	&& ! apply_filters( 'enable_edit_any_user_configuration', true )
) {
	wp_die( __( 'Sorry, you are not allowed to edit this user.' ) );
}

// Execute confirmed email change. See send_confirmation_on_profile_email().
if ( IS_PROFILE_PAGE && isset( $_GET['newuseremail'] ) && $current_user->ID ) {
	$new_email = get_user_meta( $current_user->ID, '_new_email', true );
	if ( $new_email && hash_equals( $new_email['hash'], $_GET['newuseremail'] ) ) {
		$user             = new stdClass;
		$user->ID         = $current_user->ID;
		$user->user_email = esc_html( trim( $new_email['newemail'] ) );
		if ( is_multisite() && $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $current_user->user_login ) ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $user->user_email, $current_user->user_login ) );
		}
		wp_update_user( $user );
		delete_user_meta( $current_user->ID, '_new_email' );
		wp_redirect( add_query_arg( array( 'updated' => 'true' ), self_admin_url( 'profile.php' ) ) );
		die();
	} else {
		wp_redirect( add_query_arg( array( 'error' => 'new-email' ), self_admin_url( 'profile.php' ) ) );
	}
} elseif ( IS_PROFILE_PAGE && ! empty( $_GET['dismiss'] ) && $current_user->ID . '_new_email' === $_GET['dismiss'] ) {
	check_admin_referer( 'dismiss-' . $current_user->ID . '_new_email' );
	delete_user_meta( $current_user->ID, '_new_email' );
	wp_redirect( add_query_arg( array( 'updated' => 'true' ), self_admin_url( 'profile.php' ) ) );
	die();
}

switch ( $action ) {
	case 'update':
		check_admin_referer( 'update-user_' . $user_id );

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			wp_die( __( 'Sorry, you are not allowed to edit this user.' ) );
		}

		if ( IS_PROFILE_PAGE ) {
			/**
			 * Fires before the page loads on the 'Profile' editing screen.
			 *
			 * The action only fires if the current user is editing their own profile.
			 *
			 * @since 2.0.0
			 *
			 * @param int $user_id The user ID.
			 */
			do_action( 'personal_options_update', $user_id );
		} else {
			/**
			 * Fires before the page loads on the 'Edit User' screen.
			 *
			 * @since 2.7.0
			 *
			 * @param int $user_id The user ID.
			 */
			do_action( 'edit_user_profile_update', $user_id );
		}

		// Update the email address in signups, if present.
		if ( is_multisite() ) {
			$user = get_userdata( $user_id );

			if ( $user->user_login && isset( $_POST['email'] ) && is_email( $_POST['email'] ) && $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $user->user_login ) ) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $_POST['email'], $user_login ) );
			}
		}

		// Update the user.
		$errors = edit_user( $user_id );

		// Grant or revoke super admin status if requested.
		if ( is_multisite() && is_network_admin()
			&& ! IS_PROFILE_PAGE && current_user_can( 'manage_network_options' )
			&& ! isset( $super_admins ) && empty( $_POST['super_admin'] ) === is_super_admin( $user_id )
		) {
			empty( $_POST['super_admin'] ) ? revoke_super_admin( $user_id ) : grant_super_admin( $user_id );
		}

		if ( ! is_wp_error( $errors ) ) {
			$redirect = add_query_arg( 'updated', true, get_edit_user_link( $user_id ) );
			if ( $wp_http_referer ) {
				$redirect = add_query_arg( 'wp_http_referer', urlencode( $wp_http_referer ), $redirect );
			}
			wp_redirect( $redirect );
			exit;
		}

		// Intentional fall-through to display $errors.
	default:
		$profile_user = get_user_to_edit( $user_id );

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			wp_die( __( 'Sorry, you are not allowed to edit this user.' ) );
		}

		$title    = sprintf( $title, $profile_user->display_name );
		$sessions = WP_Session_Tokens::get_instance( $profile_user->ID );

		require_once ABSPATH . 'wp-admin/admin-header.php';
		?>

		<?php if ( ! IS_PROFILE_PAGE && is_super_admin( $profile_user->ID ) && current_user_can( 'manage_network_options' ) ) : ?>
			<div class="notice notice-info"><p><strong><?php _e( 'Important:' ); ?></strong> <?php _e( 'This user has super admin privileges.' ); ?></p></div>
		<?php endif; ?>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div id="message" class="updated notice is-dismissible">
				<?php if ( IS_PROFILE_PAGE ) : ?>
					<p><strong><?php _e( 'Profile updated.' ); ?></strong></p>
				<?php else : ?>
					<p><strong><?php _e( 'User updated.' ); ?></strong></p>
				<?php endif; ?>
				<?php if ( $wp_http_referer && false === strpos( $wp_http_referer, 'user-new.php' ) && ! IS_PROFILE_PAGE ) : ?>
					<p><a href="<?php echo esc_url( wp_validate_redirect( sanitize_url( $wp_http_referer ), self_admin_url( 'users.php' ) ) ); ?>"><?php _e( '&larr; Go to Users' ); ?></a></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $_GET['error'] ) ) : ?>
			<div class="notice notice-error">
			<?php if ( 'new-email' === $_GET['error'] ) : ?>
				<p><?php _e( 'Error while saving the new email address. Please try again.' ); ?></p>
			<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $errors ) && is_wp_error( $errors ) ) : ?>
			<div class="error">
				<p><?php echo implode( "</p>\n<p>", $errors->get_error_messages() ); ?></p>
			</div>
		<?php endif; ?>

		<div class="wrap" id="profile-page">
			<h1 class="wp-heading-inline">
					<?php echo esc_html( $title ); ?>
			</h1>

			<?php if ( ! IS_PROFILE_PAGE ) : ?>
				<?php if ( current_user_can( 'create_users' ) ) : ?>
					<a href="user-new.php" class="page-title-action"><?php echo esc_html_x( 'Add New', 'user' ); ?></a>
				<?php elseif ( is_multisite() && current_user_can( 'promote_users' ) ) : ?>
					<a href="user-new.php" class="page-title-action"><?php echo esc_html_x( 'Add Existing', 'user' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>

			<hr class="wp-header-end">

			<form id="your-profile" action="<?php echo esc_url( self_admin_url( IS_PROFILE_PAGE ? 'profile.php' : 'user-edit.php' ) ); ?>" method="post" novalidate="novalidate"
				<?php
				/**
				 * Fires inside the your-profile form tag on the user editing screen.
				 *
				 * @since 3.0.0
				 */
				do_action( 'user_edit_form_tag' );
				?>
				>
				<?php wp_nonce_field( 'update-user_' . $user_id ); ?>
				<?php if ( $wp_http_referer ) : ?>
					<input type="hidden" name="wp_http_referer" value="<?php echo esc_url( $wp_http_referer ); ?>" />
				<?php endif; ?>
				<p>
					<input type="hidden" name="from" value="profile" />
					<input type="hidden" name="checkuser_id" value="<?php echo get_current_user_id(); ?>" />
				</p>

				<h2><?php _e( 'Personal Options' ); ?></h2>

				<table class="form-table" role="presentation">
					<?php if ( ! ( IS_PROFILE_PAGE && ! $user_can_edit ) ) : ?>
						<tr class="user-rich-editing-wrap">
							<th scope="row"><?php _e( 'Visual Editor' ); ?></th>
							<td>
								<label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="false" <?php checked( 'false', $profile_user->rich_editing ); ?> />
									<?php _e( 'Disable the visual editor when writing' ); ?>
								</label>
							</td>
						</tr>
					<?php endif; ?>

					<?php
					$show_syntax_highlighting_preference = (
					// For Custom HTML widget and Additional CSS in Customizer.
					user_can( $profile_user, 'edit_theme_options' )
					||
					// Edit plugins.
					user_can( $profile_user, 'edit_plugins' )
					||
					// Edit themes.
					user_can( $profile_user, 'edit_themes' )
					);
					?>

					<?php if ( $show_syntax_highlighting_preference ) : ?>
					<tr class="user-syntax-highlighting-wrap">
						<th scope="row"><?php _e( 'Syntax Highlighting' ); ?></th>
						<td>
							<label for="syntax_highlighting"><input name="syntax_highlighting" type="checkbox" id="syntax_highlighting" value="false" <?php checked( 'false', $profile_user->syntax_highlighting ); ?> />
								<?php _e( 'Disable syntax highlighting when editing code' ); ?>
							</label>
						</td>
					</tr>
					<?php endif; ?>

					<?php if ( count( $_wp_admin_css_colors ) > 1 && has_action( 'admin_color_scheme_picker' ) ) : ?>
					<tr class="user-admin-color-wrap">
						<th scope="row"><?php _e( 'Admin Color Scheme' ); ?></th>
						<td>
							<?php
							/**
							 * Fires in the 'Admin Color Scheme' section of the user editing screen.
							 *
							 * The section is only enabled if a callback is hooked to the action,
							 * and if there is more than one defined color scheme for the admin.
							 *
							 * @since 3.0.0
							 * @since 3.8.1 Added `$user_id` parameter.
							 *
							 * @param int $user_id The user ID.
							 */
							do_action( 'admin_color_scheme_picker', $user_id );
							?>
						</td>
					</tr>
					<?php endif; // End if count ( $_wp_admin_css_colors ) > 1 ?>

					<?php if ( ! ( IS_PROFILE_PAGE && ! $user_can_edit ) ) : ?>
					<tr class="user-comment-shortcuts-wrap">
						<th scope="row"><?php _e( 'Keyboard Shortcuts' ); ?></th>
						<td>
							<label for="comment_shortcuts">
								<input type="checkbox" name="comment_shortcuts" id="comment_shortcuts" value="true" <?php checked( 'true', $profile_user->comment_shortcuts ); ?> />
								<?php _e( 'Enable keyboard shortcuts for comment moderation.' ); ?>
							</label>
							<?php _e( '<a href="https://wordpress.org/support/article/keyboard-shortcuts/" target="_blank">More information</a>' ); ?>
						</td>
					</tr>
					<?php endif; ?>

					<tr class="show-admin-bar user-admin-bar-front-wrap">
						<th scope="row"><?php _e( 'Toolbar' ); ?></th>
						<td>
							<label for="admin_bar_front">
								<input name="admin_bar_front" type="checkbox" id="admin_bar_front" value="1"<?php checked( _get_admin_bar_pref( 'front', $profile_user->ID ) ); ?> />
								<?php _e( 'Show Toolbar when viewing site' ); ?>
							</label><br />
						</td>
					</tr>

					<?php $languages = get_available_languages(); ?>
					<?php if ( $languages ) : ?>
					<tr class="user-language-wrap">
						<th scope="row">
							<?php /* translators: The user language selection field label. */ ?>
							<label for="locale"><?php _e( 'Language' ); ?><span class="dashicons dashicons-translation" aria-hidden="true"></span></label>
						</th>
						<td>
							<?php
								$user_locale = $profile_user->locale;

							if ( 'en_US' === $user_locale ) {
								$user_locale = '';
							} elseif ( '' === $user_locale || ! in_array( $user_locale, $languages, true ) ) {
								$user_locale = 'site-default';
							}

							wp_dropdown_languages(
								array(
									'name'      => 'locale',
									'id'        => 'locale',
									'selected'  => $user_locale,
									'languages' => $languages,
									'show_available_translations' => false,
									'show_option_site_default' => true,
								)
							);
							?>
						</td>
					</tr>
					<?php endif; ?>

					<?php
					/**
					 * Fires at the end of the 'Personal Options' settings table on the user editing screen.
					 *
					 * @since 2.7.0
					 *
					 * @param WP_User $profile_user The current WP_User object.
					 */
					do_action( 'personal_options', $profile_user );
					?>

				</table>
				<?php
				if ( IS_PROFILE_PAGE ) {
					/**
					 * Fires after the 'Personal Options' settings table on the 'Profile' editing screen.
					 *
					 * The action only fires if the current user is editing their own profile.
					 *
					 * @since 2.0.0
					 *
					 * @param WP_User $profile_user The current WP_User object.
					 */
					do_action( 'profile_personal_options', $profile_user );
				}
				?>

				<h2><?php _e( 'Name' ); ?></h2>

				<table class="form-table" role="presentation">
					<tr class="user-user-login-wrap">
						<th><label for="user_login"><?php _e( 'Username' ); ?></label></th>
						<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profile_user->user_login ); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e( 'Usernames cannot be changed.' ); ?></span></td>
					</tr>

					<?php if ( ! IS_PROFILE_PAGE && ! is_network_admin() && current_user_can( 'promote_user', $profile_user->ID ) ) : ?>
						<tr class="user-role-wrap">
							<th><label for="role"><?php _e( 'Role' ); ?></label></th>
							<td>
								<select name="role" id="role">
									<?php
									// Compare user role against currently editable roles.
									$user_roles = array_intersect( array_values( $profile_user->roles ), array_keys( get_editable_roles() ) );
									$user_role  = reset( $user_roles );

									// Print the full list of roles with the primary one selected.
									wp_dropdown_roles( $user_role );

									// Print the 'no role' option. Make it selected if the user has no role yet.
									if ( $user_role ) {
										echo '<option value="">' . __( '&mdash; No role for this site &mdash;' ) . '</option>';
									} else {
										echo '<option value="" selected="selected">' . __( '&mdash; No role for this site &mdash;' ) . '</option>';
									}
									?>
							</select>
							</td>
						</tr>
					<?php endif; // End if ! IS_PROFILE_PAGE. ?>

					<?php if ( is_multisite() && is_network_admin() && ! IS_PROFILE_PAGE && current_user_can( 'manage_network_options' ) && ! isset( $super_admins ) ) : ?>
						<tr class="user-super-admin-wrap">
							<th><?php _e( 'Super Admin' ); ?></th>
							<td>
								<?php if ( 0 !== strcasecmp( $profile_user->user_email, get_site_option( 'admin_email' ) ) || ! is_super_admin( $profile_user->ID ) ) : ?>
									<p><label><input type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( $profile_user->ID ) ); ?> /> <?php _e( 'Grant this user super admin privileges for the Network.' ); ?></label></p>
								<?php else : ?>
									<p><?php _e( 'Super admin privileges cannot be removed because this user has the network admin email.' ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>

					<tr class="user-first-name-wrap">
						<th><label for="first_name"><?php _e( 'First Name' ); ?></label></th>
						<td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profile_user->first_name ); ?>" class="regular-text" /></td>
					</tr>

					<tr class="user-last-name-wrap">
						<th><label for="last_name"><?php _e( 'Last Name' ); ?></label></th>
						<td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profile_user->last_name ); ?>" class="regular-text" /></td>
					</tr>

					<tr class="user-nickname-wrap">
						<th><label for="nickname"><?php _e( 'Nickname' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
						<td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( $profile_user->nickname ); ?>" class="regular-text" /></td>
					</tr>

					<tr class="user-display-name-wrap">
						<th>
							<label for="display_name"><?php _e( 'Display name publicly as' ); ?></label>
						</th>
						<td>
							<select name="display_name" id="display_name">
								<?php
									$public_display                     = array();
									$public_display['display_nickname'] = $profile_user->nickname;
									$public_display['display_username'] = $profile_user->user_login;

								if ( ! empty( $profile_user->first_name ) ) {
									$public_display['display_firstname'] = $profile_user->first_name;
								}

								if ( ! empty( $profile_user->last_name ) ) {
									$public_display['display_lastname'] = $profile_user->last_name;
								}

								if ( ! empty( $profile_user->first_name ) && ! empty( $profile_user->last_name ) ) {
									$public_display['display_firstlast'] = $profile_user->first_name . ' ' . $profile_user->last_name;
									$public_display['display_lastfirst'] = $profile_user->last_name . ' ' . $profile_user->first_name;
								}

								if ( ! in_array( $profile_user->display_name, $public_display, true ) ) { // Only add this if it isn't duplicated elsewhere.
									$public_display = array( 'display_displayname' => $profile_user->display_name ) + $public_display;
								}

								$public_display = array_map( 'trim', $public_display );
								$public_display = array_unique( $public_display );

								?>
								<?php foreach ( $public_display as $id => $item ) : ?>
									<option <?php selected( $profile_user->display_name, $item ); ?>><?php echo $item; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>

				<h2><?php _e( 'Contact Info' ); ?></h2>

				<table class="form-table" role="presentation">
					<tr class="user-email-wrap">
						<th><label for="email"><?php _e( 'Email' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
						<td>
							<input type="email" name="email" id="email" aria-describedby="email-description" value="<?php echo esc_attr( $profile_user->user_email ); ?>" class="regular-text ltr" />
							<?php if ( $profile_user->ID === $current_user->ID ) : ?>
								<p class="description" id="email-description">
									<?php _e( 'If you change this, an email will be sent at your new address to confirm it. <strong>The new address will not become active until confirmed.</strong>' ); ?>
								</p>
							<?php endif; ?>

							<?php $new_email = get_user_meta( $current_user->ID, '_new_email', true ); ?>
							<?php if ( $new_email && $new_email['newemail'] !== $current_user->user_email && $profile_user->ID === $current_user->ID ) : ?>
							<div class="updated inline">
								<p>
									<?php
									printf(
										/* translators: %s: New email. */
										__( 'There is a pending change of your email to %s.' ),
										'<code>' . esc_html( $new_email['newemail'] ) . '</code>'
									);
									printf(
										' <a href="%1$s">%2$s</a>',
										esc_url( wp_nonce_url( self_admin_url( 'profile.php?dismiss=' . $current_user->ID . '_new_email' ), 'dismiss-' . $current_user->ID . '_new_email' ) ),
										__( 'Cancel' )
									);
									?>
								</p>
							</div>
							<?php endif; ?>
						</td>
					</tr>

					<tr class="user-url-wrap">
						<th><label for="url"><?php _e( 'Website' ); ?></label></th>
						<td><input type="url" name="url" id="url" value="<?php echo esc_attr( $profile_user->user_url ); ?>" class="regular-text code" /></td>
					</tr>

					<?php foreach ( wp_get_user_contact_methods( $profile_user ) as $name => $desc ) : ?>
					<tr class="user-<?php echo $name; ?>-wrap">
						<th>
							<label for="<?php echo $name; ?>">
							<?php
							/**
							 * Filters a user contactmethod label.
							 *
							 * The dynamic portion of the hook name, `$name`, refers to
							 * each of the keys in the contact methods array.
							 *
							 * @since 2.9.0
							 *
							 * @param string $desc The translatable label for the contact method.
							 */
							echo apply_filters( "user_{$name}_label", $desc );
							?>
							</label>
						</th>
						<td>
							<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $profile_user->$name ); ?>" class="regular-text" />
						</td>
					</tr>
					<?php endforeach; ?>
				</table>

				<h2><?php IS_PROFILE_PAGE ? _e( 'About Yourself' ) : _e( 'About the user' ); ?></h2>

				<table class="form-table" role="presentation">
					<tr class="user-description-wrap">
						<th><label for="description"><?php _e( 'Biographical Info' ); ?></label></th>
						<td><textarea name="description" id="description" rows="5" cols="30"><?php echo $profile_user->description; // textarea_escaped ?></textarea>
						<p class="description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.' ); ?></p></td>
					</tr>

					<?php if ( get_option( 'show_avatars' ) ) : ?>
						<tr class="user-profile-picture">
							<th><?php _e( 'Profile Picture' ); ?></th>
							<td>
								<?php echo get_avatar( $user_id ); ?>
								<p class="description">
									<?php
									if ( IS_PROFILE_PAGE ) {
										$description = sprintf(
											/* translators: %s: Gravatar URL. */
											__( '<a href="%s">You can change your profile picture on Gravatar</a>.' ),
											__( 'https://en.gravatar.com/' )
										);
									} else {
										$description = '';
									}

									/**
									 * Filters the user profile picture description displayed under the Gravatar.
									 *
									 * @since 4.4.0
									 * @since 4.7.0 Added the `$profile_user` parameter.
									 *
									 * @param string  $description  The description that will be printed.
									 * @param WP_User $profile_user The current WP_User object.
									 */
									echo apply_filters( 'user_profile_picture_description', $description, $profile_user );
									?>
								</p>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					/**
					 * Filters the display of the password fields.
					 *
					 * @since 1.5.1
					 * @since 2.8.0 Added the `$profile_user` parameter.
					 * @since 4.4.0 Now evaluated only in user-edit.php.
					 *
					 * @param bool    $show         Whether to show the password fields. Default true.
					 * @param WP_User $profile_user User object for the current user to edit.
					 */
					$show_password_fields = apply_filters( 'show_password_fields', true, $profile_user );
					?>
					<?php if ( $show_password_fields ) : ?>
						</table>

						<h2><?php _e( 'Account Management' ); ?></h2>

						<table class="form-table" role="presentation">
							<tr id="password" class="user-pass1-wrap">
								<th><label for="pass1"><?php _e( 'New Password' ); ?></label></th>
								<td>
									<input class="hidden" value=" " /><!-- #24364 workaround -->
									<button type="button" class="button wp-generate-pw hide-if-no-js" aria-expanded="false"><?php _e( 'Set New Password' ); ?></button>
									<div class="wp-pwd hide-if-js">
										<span class="password-input-wrapper">
											<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="new-password" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
										</span>
										<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
											<span class="dashicons dashicons-hidden" aria-hidden="true"></span>
											<span class="text"><?php _e( 'Hide' ); ?></span>
										</button>
										<button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
											<span class="dashicons dashicons-no" aria-hidden="true"></span>
											<span class="text"><?php _e( 'Cancel' ); ?></span>
										</button>
										<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
									</div>
								</td>
							</tr>
							<tr class="user-pass2-wrap hide-if-js">
								<th scope="row"><label for="pass2"><?php _e( 'Repeat New Password' ); ?></label></th>
								<td>
								<input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="new-password" aria-describedby="pass2-desc" />
									<?php if ( IS_PROFILE_PAGE ) : ?>
										<p class="description" id="pass2-desc"><?php _e( 'Type your new password again.' ); ?></p>
									<?php else : ?>
										<p class="description" id="pass2-desc"><?php _e( 'Type the new password again.' ); ?></p>
									<?php endif; ?>
								</td>
							</tr>
							<tr class="pw-weak">
								<th><?php _e( 'Confirm Password' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="pw_weak" class="pw-checkbox" />
										<span id="pw-weak-text-label"><?php _e( 'Confirm use of weak password' ); ?></span>
									</label>
								</td>
							</tr>
							<?php endif; // End Show Password Fields. ?>

							<?php // Allow admins to send reset password link. ?>
							<?php if ( ! IS_PROFILE_PAGE ) : ?>
								<tr class="user-generate-reset-link-wrap hide-if-no-js">
									<th><?php _e( 'Password Reset' ); ?></th>
									<td>
										<div class="generate-reset-link">
											<button type="button" class="button button-secondary" id="generate-reset-link">
												<?php _e( 'Send Reset Link' ); ?>
											</button>
										</div>
										<p class="description">
											<?php
											printf(
												/* translators: %s: User's display name. */
												__( 'Send %s a link to reset their password. This will not change their password, nor will it force a change.' ),
												esc_html( $profile_user->display_name )
											);
											?>
										</p>
									</td>
								</tr>
							<?php endif; ?>

							<?php if ( IS_PROFILE_PAGE && count( $sessions->get_all() ) === 1 ) : ?>
								<tr class="user-sessions-wrap hide-if-no-js">
									<th><?php _e( 'Sessions' ); ?></th>
									<td aria-live="assertive">
										<div class="destroy-sessions"><button type="button" disabled class="button"><?php _e( 'Log Out Everywhere Else' ); ?></button></div>
										<p class="description">
											<?php _e( 'You are only logged in at this location.' ); ?>
										</p>
									</td>
								</tr>
							<?php elseif ( IS_PROFILE_PAGE && count( $sessions->get_all() ) > 1 ) : ?>
								<tr class="user-sessions-wrap hide-if-no-js">
									<th><?php _e( 'Sessions' ); ?></th>
									<td aria-live="assertive">
										<div class="destroy-sessions"><button type="button" class="button" id="destroy-sessions"><?php _e( 'Log Out Everywhere Else' ); ?></button></div>
										<p class="description">
											<?php _e( 'Did you lose your phone or leave your account logged in at a public computer? You can log out everywhere else, and stay logged in here.' ); ?>
										</p>
									</td>
								</tr>
							<?php elseif ( ! IS_PROFILE_PAGE && $sessions->get_all() ) : ?>
								<tr class="user-sessions-wrap hide-if-no-js">
									<th><?php _e( 'Sessions' ); ?></th>
									<td>
										<p><button type="button" class="button" id="destroy-sessions"><?php _e( 'Log Out Everywhere' ); ?></button></p>
										<p class="description">
											<?php
											/* translators: %s: User's display name. */
											printf( __( 'Log %s out of all locations.' ), $profile_user->display_name );
											?>
										</p>
									</td>
								</tr>
							<?php endif; ?>
						</table>

					<?php if ( wp_is_application_passwords_available_for_user( $user_id ) || ! wp_is_application_passwords_supported() ) : ?>
						<div class="application-passwords hide-if-no-js" id="application-passwords-section">
							<h2><?php _e( 'Application Passwords' ); ?></h2>
							<p><?php _e( 'Application passwords allow authentication via non-interactive systems, such as XML-RPC or the REST API, without providing your actual password. Application passwords can be easily revoked. They cannot be used for traditional logins to your website.' ); ?></p>
							<?php if ( wp_is_application_passwords_available_for_user( $user_id ) ) : ?>
								<?php
								if ( is_multisite() ) :
									$blogs       = get_blogs_of_user( $user_id, true );
									$blogs_count = count( $blogs );

									if ( $blogs_count > 1 ) :
										?>
										<p>
											<?php
											/* translators: 1: URL to my-sites.php, 2: Number of sites the user has. */
											$message = _n(
												'Application passwords grant access to <a href="%1$s">the %2$s site in this installation that you have permissions on</a>.',
												'Application passwords grant access to <a href="%1$s">all %2$s sites in this installation that you have permissions on</a>.',
												$blogs_count
											);

											if ( is_super_admin( $user_id ) ) {
												/* translators: 1: URL to my-sites.php, 2: Number of sites the user has. */
												$message = _n(
													'Application passwords grant access to <a href="%1$s">the %2$s site on the network as you have Super Admin rights</a>.',
													'Application passwords grant access to <a href="%1$s">all %2$s sites on the network as you have Super Admin rights</a>.',
													$blogs_count
												);
											}

											printf(
												$message,
												admin_url( 'my-sites.php' ),
												number_format_i18n( $blogs_count )
											);
											?>
										</p>
										<?php
									endif;
								endif;
								?>

								<?php if ( ! wp_is_site_protected_by_basic_auth( 'front' ) ) : ?>
									<div class="create-application-password form-wrap">
										<div class="form-field">
											<label for="new_application_password_name"><?php _e( 'New Application Password Name' ); ?></label>
											<input type="text" size="30" id="new_application_password_name" name="new_application_password_name" class="input" aria-required="true" aria-describedby="new_application_password_name_desc" />
											<p class="description" id="new_application_password_name_desc"><?php _e( 'Required to create an Application Password, but not to update the user.' ); ?></p>
										</div>

										<?php
										/**
										 * Fires in the create Application Passwords form.
										 *
										 * @since 5.6.0
										 *
										 * @param WP_User $profile_user The current WP_User object.
										 */
										do_action( 'wp_create_application_password_form', $profile_user );
										?>

										<button type="button" name="do_new_application_password" id="do_new_application_password" class="button button-secondary"><?php _e( 'Add New Application Password' ); ?></button>
									</div>
								<?php else : ?>
									<div class="notice notice-error inline">
										<p><?php _e( 'Your website appears to use Basic Authentication, which is not currently compatible with Application Passwords.' ); ?></p>
									</div>
								<?php endif; ?>

								<div class="application-passwords-list-table-wrapper">
									<?php
									$application_passwords_list_table = _get_list_table( 'WP_Application_Passwords_List_Table', array( 'screen' => 'application-passwords-user' ) );
									$application_passwords_list_table->prepare_items();
									$application_passwords_list_table->display();
									?>
								</div>
							<?php elseif ( ! wp_is_application_passwords_supported() ) : ?>
								<p><?php _e( 'The application password feature requires HTTPS, which is not enabled on this site.' ); ?></p>
								<p>
									<?php
									printf(
										/* translators: %s: Documentation URL. */
										__( 'If this is a development website you can <a href="%s" target="_blank">set the environment type accordingly</a> to enable application passwords.' ),
										__( 'https://developer.wordpress.org/apis/wp-config-php/#wp-environment-type' )
									);
									?>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; // End Application Passwords. ?>

					<?php
					if ( IS_PROFILE_PAGE ) {
						/**
						 * Fires after the 'About Yourself' settings table on the 'Profile' editing screen.
						 *
						 * The action only fires if the current user is editing their own profile.
						 *
						 * @since 2.0.0
						 *
						 * @param WP_User $profile_user The current WP_User object.
						 */
						do_action( 'show_user_profile', $profile_user );
					} else {
						/**
						 * Fires after the 'About the User' settings table on the 'Edit User' screen.
						 *
						 * @since 2.0.0
						 *
						 * @param WP_User $profile_user The current WP_User object.
						 */
						do_action( 'edit_user_profile', $profile_user );
					}
					?>

					<?php
					/**
					 * Filters whether to display additional capabilities for the user.
					 *
					 * The 'Additional Capabilities' section will only be enabled if
					 * the number of the user's capabilities exceeds their number of
					 * roles.
					 *
					 * @since 2.8.0
					 *
					 * @param bool    $enable      Whether to display the capabilities. Default true.
					 * @param WP_User $profile_user The current WP_User object.
					 */
					$display_additional_caps = apply_filters( 'additional_capabilities_display', true, $profile_user );
					?>

				<?php if ( count( $profile_user->caps ) > count( $profile_user->roles ) && ( true === $display_additional_caps ) ) : ?>
					<h2><?php _e( 'Additional Capabilities' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr class="user-capabilities-wrap">
							<th scope="row"><?php _e( 'Capabilities' ); ?></th>
							<td>
								<?php
								$output = '';
								foreach ( $profile_user->caps as $cap => $value ) {
									if ( ! $wp_roles->is_role( $cap ) ) {
										if ( '' !== $output ) {
											$output .= ', ';
										}

										if ( $value ) {
											$output .= $cap;
										} else {
											/* translators: %s: Capability name. */
											$output .= sprintf( __( 'Denied: %s' ), $cap );
										}
									}
								}
								echo $output;
								?>
							</td>
						</tr>
					</table>
				<?php endif; // End Display Additional Capabilities. ?>

				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />

				<?php submit_button( IS_PROFILE_PAGE ? __( 'Update Profile' ) : __( 'Update User' ) ); ?>

			</form>
		</div>
		<?php
		break;
}
?>
<script type="text/javascript">
	if (window.location.hash == '#password') {
		document.getElementById('pass1').focus();
	}
</script>

<?php if ( isset( $application_passwords_list_table ) ) : ?>
	<script type="text/html" id="tmpl-new-application-password">
		<div class="notice notice-success is-dismissible new-application-password-notice" role="alert" tabindex="-1">
			<p class="application-password-display">
				<label for="new-application-password-value">
					<?php
					printf(
						/* translators: %s: Application name. */
						__( 'Your new password for %s is:' ),
						'<strong>{{ data.name }}</strong>'
					);
					?>
				</label>
				<input id="new-application-password-value" type="text" class="code" readonly="readonly" value="{{ data.password }}" />
			</p>
			<p><?php _e( 'Be sure to save this in a safe location. You will not be able to retrieve it.' ); ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php _e( 'Dismiss this notice.' ); ?></span>
			</button>
		</div>
	</script>

	<script type="text/html" id="tmpl-application-password-row">
		<?php $application_passwords_list_table->print_js_template_row(); ?>
	</script>
<?php endif; ?>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
