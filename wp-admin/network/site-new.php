<?php
/**
 * Add Site Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

/** WordPress Translation Install API */
require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'You do not have sufficient permissions to add sites to this network.' ) );

	get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __('Overview'),
		'content' =>
			'<p>' . __('This screen is for Super Admins to add new sites to the network. This is not affected by the registration settings.') . '</p>' .
			'<p>' . __('If the admin email for the new site does not exist in the database, a new user will also be created.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Network_Admin_Sites_Screen" target="_blank">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

if ( isset($_REQUEST['action']) && 'add-site' == $_REQUEST['action'] ) {
	check_admin_referer( 'add-blog', '_wpnonce_add-blog' );

	if ( ! is_array( $_POST['blog'] ) )
		wp_die( __( 'Can&#8217;t create an empty site.' ) );

	$blog = $_POST['blog'];
	$domain = '';
	if ( preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) )
		$domain = strtolower( $blog['domain'] );

	// If not a subdomain install, make sure the domain isn't a reserved word
	if ( ! is_subdomain_install() ) {
		/** This filter is documented in wp-includes/ms-functions.php */
		$subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
		if ( in_array( $domain, $subdirectory_reserved_names ) )
			wp_die( sprintf( __('The following words are reserved for use by WordPress functions and cannot be used as blog names: <code>%s</code>' ), implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
	}

	$title = $blog['title'];

	$meta = array(
		'public' => 1
	);

	// Handle translation install for the new site.
	if ( ! empty( $_POST['WPLANG'] ) && wp_can_install_language_pack() ) {
		$language = wp_download_language_pack( wp_unslash( $_POST['WPLANG'] ) );
		if ( $language ) {
			$meta['WPLANG'] = $language;
		}
	}

	if ( empty( $domain ) )
		wp_die( __( 'Missing or invalid site address.' ) );

	if ( isset( $blog['email'] ) && '' === trim( $blog['email'] ) ) {
		wp_die( __( 'Missing email address.' ) );
	}

	$email = sanitize_email( $blog['email'] );
	if ( ! is_email( $email ) ) {
		wp_die( __( 'Invalid email address.' ) );
	}

	if ( is_subdomain_install() ) {
		$newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
		$path      = $current_site->path;
	} else {
		$newdomain = $current_site->domain;
		$path      = $current_site->path . $domain . '/';
	}

	$password = 'N/A';
	$user_id = email_exists($email);
	if ( !$user_id ) { // Create a new user with a random password
		$password = wp_generate_password( 12, false );
		$user_id = wpmu_create_user( $domain, $password, $email );
		if ( false === $user_id )
			wp_die( __( 'There was an error creating the user.' ) );
		else
			wp_new_user_notification( $user_id, 'both' );
	}

	$wpdb->hide_errors();
	$id = wpmu_create_blog( $newdomain, $path, $title, $user_id, $meta, $current_site->id );
	$wpdb->show_errors();
	if ( ! is_wp_error( $id ) ) {
		if ( ! is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) ) {
			update_user_option( $user_id, 'primary_blog', $id, true );
		}

		$content_mail = sprintf(
			/* translators: 1: user login, 2: site url, 3: site name/title */
			__( 'New site created by %1$s

Address: %2$s
Name: %3$s' ),
			$current_user->user_login,
			get_site_url( $id ),
			wp_unslash( $title )
		);
		wp_mail( get_site_option('admin_email'), sprintf( __( '[%s] New Site Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
		wpmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
		wp_redirect( add_query_arg( array( 'update' => 'added', 'id' => $id ), 'site-new.php' ) );
		exit;
	} else {
		wp_die( $id->get_error_message() );
	}
}

if ( isset($_GET['update']) ) {
	$messages = array();
	if ( 'added' == $_GET['update'] )
		$messages[] = sprintf(
			/* translators: 1: dashboard url, 2: network admin edit url */
			__( 'Site added. <a href="%1$s">Visit Dashboard</a> or <a href="%2$s">Edit Site</a>' ),
			esc_url( get_admin_url( absint( $_GET['id'] ) ) ),
			network_admin_url( 'site-info.php?id=' . absint( $_GET['id'] ) )
		);
}

$title = __('Add New Site');
$parent_file = 'sites.php';

wp_enqueue_script( 'user-suggest' );

require( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap">
<h1 id="add-new-site"><?php _e( 'Add New Site' ); ?></h1>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg )
		echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
} ?>
<form method="post" action="<?php echo network_admin_url( 'site-new.php?action=add-site' ); ?>" novalidate="novalidate">
<?php wp_nonce_field( 'add-blog', '_wpnonce_add-blog' ) ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="site-address"><?php _e( 'Site Address' ) ?></label></th>
			<td>
			<?php if ( is_subdomain_install() ) { ?>
				<input name="blog[domain]" type="text" class="regular-text" id="site-address" aria-describedby="site-address-desc" autocapitalize="none" autocorrect="off"/><span class="no-break">.<?php echo preg_replace( '|^www\.|', '', $current_site->domain ); ?></span>
			<?php } else {
				echo $current_site->domain . $current_site->path ?><input name="blog[domain]" type="text" class="regular-text" id="site-address" aria-describedby="site-address-desc"  autocapitalize="none" autocorrect="off" />
			<?php }
			echo '<p id="site-address-desc">' . __( 'Only lowercase letters (a-z) and numbers are allowed.' ) . '</p>';
			?>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="site-title"><?php _e( 'Site Title' ) ?></label></th>
			<td><input name="blog[title]" type="text" class="regular-text" id="site-title" /></td>
		</tr>
		<?php
		$languages    = get_available_languages();
		$translations = wp_get_available_translations();
		if ( ! empty( $languages ) || ! empty( $translations ) ) :
			?>
			<tr class="form-field form-required">
				<th scope="row"><label for="site-language"><?php _e( 'Site Language' ); ?></label></th>
				<td>
					<?php
					// Network default.
					$lang = get_site_option( 'WPLANG' );

					// Use English if the default isn't available.
					if ( ! in_array( $lang, $languages ) ) {
						$lang = '';
					}

					wp_dropdown_languages( array(
						'name'                        => 'WPLANG',
						'id'                          => 'site-language',
						'selected'                    => $lang,
						'languages'                   => $languages,
						'translations'                => $translations,
						'show_available_translations' => wp_can_install_language_pack(),
					) );
					?>
				</td>
			</tr>
		<?php endif; // Languages. ?>
		<tr class="form-field form-required">
			<th scope="row"><label for="admin-email"><?php _e( 'Admin Email' ) ?></label></th>
			<td><input name="blog[email]" type="email" class="regular-text wp-suggest-user" id="admin-email" data-autocomplete-type="search" data-autocomplete-field="user_email" /></td>
		</tr>
		<tr class="form-field">
			<td colspan="2"><?php _e( 'A new user will be created if the above email address is not in the database.' ) ?><br /><?php _e( 'The username and password will be mailed to this email address.' ) ?></td>
		</tr>
	</table>
	<?php submit_button( __('Add Site'), 'primary', 'add-site' ); ?>
	</form>
</div>
<?php
require( ABSPATH . 'wp-admin/admin-footer.php' );
