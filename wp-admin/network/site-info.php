<?php
/**
 * Edit Site Info Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! is_multisite() ) {
	wp_die( __( 'Multisite support is not enabled.' ) );
}

if ( ! current_user_can( 'manage_sites' ) ) {
	wp_die( __( 'Sorry, you are not allowed to edit this site.' ) );
}

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __( 'Overview' ),
	'content' =>
		'<p>' . __( 'The menu is for editing information specific to individual sites, particularly if the admin area of a site is unavailable.' ) . '</p>' .
		'<p>' . __( '<strong>Info</strong> &mdash; The site URL is rarely edited as this can cause the site to not work properly. The Registered date and Last Updated date are displayed. Network admins can mark a site as archived, spam, deleted and mature, to remove from public listings or disable.' ) . '</p>' .
		'<p>' . __( '<strong>Users</strong> &mdash; This displays the users associated with this site. You can also change their role, reset their password, or remove them from the site. Removing the user from the site does not remove the user from the network.' ) . '</p>' .
		'<p>' . sprintf( __( '<strong>Themes</strong> &mdash; This area shows themes that are not already enabled across the network. Enabling a theme in this menu makes it accessible to this site. It does not activate the theme, but allows it to show in the site&#8217;s Appearance menu. To enable a theme for the entire network, see the <a href="%s">Network Themes</a> screen.' ), network_admin_url( 'themes.php' ) ) . '</p>' .
		'<p>' . __( '<strong>Settings</strong> &mdash; This page shows a list of all settings associated with this site. Some are created by WordPress and others are created by plugins you activate. Note that some fields are grayed out and say Serialized Data. You cannot modify these values due to the way the setting is stored in the database.' ) . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Network_Admin_Sites_Screen" target="_blank">Documentation on Site Management</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>' ) . '</p>'
);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id ) {
	wp_die( __('Invalid site ID.') );
}

$details = get_blog_details( $id );
if ( ! $details ) {
	wp_die( __( 'The requested site does not exist.' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

$parsed_scheme = parse_url( $details->siteurl, PHP_URL_SCHEME );
$is_main_site = is_main_site( $id );

if ( isset( $_REQUEST['action'] ) && 'update-site' == $_REQUEST['action'] ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	// Rewrite rules can't be flushed during switch to blog.
	delete_option( 'rewrite_rules' );

	$blog_data = wp_unslash( $_POST['blog'] );
	$blog_data['scheme'] = $parsed_scheme;

	if ( $is_main_site ) {
		// On the network's main site, don't allow the domain or path to change.
		$blog_data['domain'] = $details->domain;
		$blog_data['path'] = $details->path;
	} else {
		// For any other site, the scheme, domain, and path can all be changed. We first
		// need to ensure a scheme has been provided, otherwise fallback to the existing.
		$new_url_scheme = parse_url( $blog_data['url'], PHP_URL_SCHEME );

		if ( ! $new_url_scheme ) {
			$blog_data['url'] = esc_url( $parsed_scheme . '://' . $blog_data['url'] );
		}
		$update_parsed_url = parse_url( $blog_data['url'] );

		// If a path is not provided, use the default of `/`.
		if ( ! isset( $update_parsed_url['path'] ) ) {
			$update_parsed_url['path'] = '/';
		}

		$blog_data['scheme'] = $update_parsed_url['scheme'];
		$blog_data['domain'] = $update_parsed_url['host'];
		$blog_data['path'] = $update_parsed_url['path'];
	}

	$existing_details = get_blog_details( $id, false );
	$blog_data_checkboxes = array( 'public', 'archived', 'spam', 'mature', 'deleted' );
	foreach ( $blog_data_checkboxes as $c ) {
		if ( ! in_array( $existing_details->$c, array( 0, 1 ) ) ) {
			$blog_data[ $c ] = $existing_details->$c;
		} else {
			$blog_data[ $c ] = isset( $_POST['blog'][ $c ] ) ? 1 : 0;
		}
	}

	update_blog_details( $id, $blog_data );

	// Maybe update home and siteurl options.
	$new_details = get_blog_details( $id, false );

	$old_home_url = trailingslashit( esc_url( get_option( 'home' ) ) );
	$old_home_parsed = parse_url( $old_home_url );

	if ( $old_home_parsed['host'] === $existing_details->domain && $old_home_parsed['path'] === $existing_details->path ) {
		$new_home_url = untrailingslashit( esc_url_raw( $blog_data['scheme'] . '://' . $new_details->domain . $new_details->path ) );
		update_option( 'home', $new_home_url );
	}

	$old_site_url = trailingslashit( esc_url( get_option( 'siteurl' ) ) );
	$old_site_parsed = parse_url( $old_site_url );

	if ( $old_site_parsed['host'] === $existing_details->domain && $old_site_parsed['path'] === $existing_details->path ) {
		$new_site_url = untrailingslashit( esc_url_raw( $blog_data['scheme'] . '://' . $new_details->domain . $new_details->path ) );
		update_option( 'siteurl', $new_site_url );
	}

	restore_current_blog();
	wp_redirect( add_query_arg( array( 'update' => 'updated', 'id' => $id ), 'site-info.php' ) );
	exit;
}

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( 'updated' == $_GET['update'] ) {
		$messages[] = __( 'Site info updated.' );
	}
}

/* translators: %s: site name */
$title = sprintf( __( 'Edit Site: %s' ), esc_html( $details->blogname ) );

$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap">
<h1 id="edit-site"><?php echo $title; ?></h1>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( 'Visit' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( 'Dashboard' ); ?></a></p>
<?php

network_edit_site_nav( array(
	'blog_id'  => $id,
	'selected' => 'site-info'
) );

if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg ) {
		echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
	}
}
?>
<form method="post" action="site-info.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<?php
		// The main site of the network should not be updated on this page.
		if ( $is_main_site ) : ?>
		<tr class="form-field">
			<th scope="row"><?php _e( 'Site Address (URL)' ); ?></th>
			<td><?php echo esc_url( $details->domain . $details->path ); ?></td>
		</tr>
		<?php
		// For any other site, the scheme, domain, and path can all be changed.
		else : ?>
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Site Address (URL)' ); ?></th>
			<td><input name="blog[url]" type="text" id="url" value="<?php echo $parsed_scheme . '://' . esc_attr( $details->domain ) . esc_attr( $details->path ); ?>" /></td>
		</tr>
		<?php endif; ?>

		<tr class="form-field">
			<th scope="row"><label for="blog_registered"><?php _ex( 'Registered', 'site' ) ?></label></th>
			<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr( $details->registered ) ?>" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="blog_last_updated"><?php _e( 'Last Updated' ); ?></label></th>
			<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr( $details->last_updated ) ?>" /></td>
		</tr>
		<?php
		$attribute_fields = array( 'public' => __( 'Public' ) );
		if ( ! $is_main_site ) {
			$attribute_fields['archived'] = __( 'Archived' );
			$attribute_fields['spam']     = _x( 'Spam', 'site' );
			$attribute_fields['deleted']  = __( 'Deleted' );
		}
		$attribute_fields['mature'] = __( 'Mature' );
		?>
		<tr>
			<th scope="row"><?php _e( 'Attributes' ); ?></th>
			<td>
			<fieldset>
			<legend class="screen-reader-text"><?php _e( 'Set site attributes' ) ?></legend>
			<?php foreach ( $attribute_fields as $field_key => $field_label ) : ?>
				<label><input type="checkbox" name="blog[<?php echo $field_key; ?>]" value="1" <?php checked( (bool) $details->$field_key, true ); disabled( ! in_array( $details->$field_key, array( 0, 1 ) ) ); ?> />
				<?php echo $field_label; ?></label><br/>
			<?php endforeach; ?>
			<fieldset>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>

</div>
<?php
require( ABSPATH . 'wp-admin/admin-footer.php' );
