<?php

/**
 * Add Site Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can('manage_sites') )
	wp_die(__('You do not have sufficient permissions to add sites to this network.'));

if ( isset($_REQUEST['action']) && 'add-site' == $_REQUEST['action'] ) {
	check_admin_referer( 'add-blog', '_wpnonce_add-blog' );

	if ( ! current_user_can( 'manage_sites' ) )
		wp_die( __( 'You do not have permission to access this page.' ) );

	if ( is_array( $_POST['blog'] ) == false )
		wp_die(  __( 'Can&#8217;t create an empty site.' ) );
	$blog = $_POST['blog'];
	$domain = '';
	if ( ! preg_match( '/(--)/', $blog['domain'] ) && preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) )
		$domain = strtolower( $blog['domain'] );

	// If not a subdomain install, make sure the domain isn't a reserved word
	if ( ! is_subdomain_install() ) {
		$subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
		if ( in_array( $domain, $subdirectory_reserved_names ) )
			wp_die( sprintf( __('The following words are reserved for use by WordPress functions and cannot be used as blog names: <code>%s</code>' ), implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
	}

	$email = sanitize_email( $blog['email'] );
	$title = $blog['title'];

	if ( empty( $domain ) )
		wp_die( __( 'Missing or invalid site address.' ) );
	if ( empty( $email ) )
		wp_die( __( 'Missing email address.' ) );
	if ( !is_email( $email ) )
		wp_die( __( 'Invalid email address.' ) );

	if ( is_subdomain_install() ) {
		$newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
		$path = $base;
	} else {
		$newdomain = $current_site->domain;
		$path = $base . $domain . '/';
	}

	$password = 'N/A';
	$user_id = email_exists($email);
	if ( !$user_id ) { // Create a new user with a random password
		$password = wp_generate_password();
		$user_id = wpmu_create_user( $domain, $password, $email );
		if ( false == $user_id )
			wp_die( __( 'There was an error creating the user.' ) );
		else
			wp_new_user_notification( $user_id, $password );
	}

	$wpdb->hide_errors();
	$id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( 'public' => 1 ), $current_site->id );
	$wpdb->show_errors();
	if ( !is_wp_error( $id ) ) {
		if ( !is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) )
			update_user_option( $user_id, 'primary_blog', $id, true );
		$content_mail = sprintf( __( "New site created by %1s\n\nAddress: http://%2s\nName: %3s"), $current_user->user_login , $newdomain . $path, stripslashes( $title ) );
		wp_mail( get_site_option('admin_email'),  sprintf( __( '[%s] New Site Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
		wpmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
		wp_redirect( add_query_arg( array('update' => 'added'), 'site-new.php' ) );
		exit;
	} else {
		wp_die( $id->get_error_message() );
	}
}

if ( isset($_GET['update']) ) {
	$messages = array();
	if ( 'added' == $_GET['update'] )
		$messages[] = __('Site added.');
}

$title = __('Add New Site');
$parent_file = 'sites.php';

require('../admin-header.php');

?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="add-new-site"><?php _e('Add New Site') ?></h2>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg )
		echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
} ?>
<form method="post" action="<?php echo network_admin_url('site-new.php?action=add-site'); ?>">
<?php wp_nonce_field( 'add-blog', '_wpnonce_add-blog' ) ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Site Address' ) ?></th>
			<td>
			<?php if ( is_subdomain_install() ) { ?>
				<input name="blog[domain]" type="text" class="regular-text" title="<?php _e( 'Domain' ) ?>"/>.<?php echo preg_replace( '|^www\.|', '', $current_site->domain );?>
			<?php } else {
				echo $current_site->domain . $current_site->path ?><input name="blog[domain]" class="regular-text" type="text" title="<?php _e( 'Domain' ) ?>"/>
			<?php }
			echo '<p>' . __( 'Only the characters a-z and 0-9 recommended.' ) . '</p>';
			?>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Site Title' ) ?></th>
			<td><input name="blog[title]" type="text" class="regular-text" title="<?php _e( 'Title' ) ?>"/></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Admin Email' ) ?></th>
			<td><input name="blog[email]" type="text" class="regular-text" title="<?php _e( 'Email' ) ?>"/></td>
		</tr>
		<tr class="form-field">
			<td colspan="2"><?php _e( 'A new user will be created if the above email address is not in the database.' ) ?><br /><?php _e( 'The username and password will be mailed to this email address.' ) ?></td>
		</tr>
	</table>
	<?php submit_button( __('Add Site'), 'primary', 'add-site' ); ?>
	</form>
</div>
<?php
require('../admin-footer.php');
?>
