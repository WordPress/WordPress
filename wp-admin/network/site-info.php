<?php
/**
 * Edit Site Info Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	wp_die( __( 'Sorry, you are not allowed to edit this site.' ) );
}

get_current_screen()->add_help_tab( get_site_screen_help_tab_args() );
get_current_screen()->set_help_sidebar( get_site_screen_help_sidebar_content() );

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( ! $id ) {
	wp_die( __( 'Invalid site ID.' ) );
}

$details = get_site( $id );
if ( ! $details ) {
	wp_die( __( 'The requested site does not exist.' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

$parsed_scheme = parse_url( $details->siteurl, PHP_URL_SCHEME );
$is_main_site  = is_main_site( $id );

if ( isset( $_REQUEST['action'] ) && 'update-site' === $_REQUEST['action'] ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	// Rewrite rules can't be flushed during switch to blog.
	delete_option( 'rewrite_rules' );

	$blog_data           = wp_unslash( $_POST['blog'] );
	$blog_data['scheme'] = $parsed_scheme;

	if ( $is_main_site ) {
		// On the network's main site, don't allow the domain or path to change.
		$blog_data['domain'] = $details->domain;
		$blog_data['path']   = $details->path;
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

		// Make sure to not lose the port if it was provided.
		$blog_data['domain'] = $update_parsed_url['host'];
		if ( isset( $update_parsed_url['port'] ) ) {
			$blog_data['domain'] .= ':' . $update_parsed_url['port'];
		}

		$blog_data['path'] = $update_parsed_url['path'];
	}

	$existing_details     = get_site( $id );
	$blog_data_checkboxes = array( 'public', 'archived', 'spam', 'mature', 'deleted' );

	foreach ( $blog_data_checkboxes as $c ) {
		if ( ! in_array( (int) $existing_details->$c, array( 0, 1 ), true ) ) {
			$blog_data[ $c ] = $existing_details->$c;
		} else {
			$blog_data[ $c ] = isset( $_POST['blog'][ $c ] ) ? 1 : 0;
		}
	}

	update_blog_details( $id, $blog_data );

	// Maybe update home and siteurl options.
	$new_details = get_site( $id );

	$old_home_url    = trailingslashit( esc_url( get_option( 'home' ) ) );
	$old_home_parsed = parse_url( $old_home_url );
	$old_home_host   = $old_home_parsed['host'] . ( isset( $old_home_parsed['port'] ) ? ':' . $old_home_parsed['port'] : '' );

	if ( $old_home_host === $existing_details->domain && $old_home_parsed['path'] === $existing_details->path ) {
		$new_home_url = untrailingslashit( sanitize_url( $blog_data['scheme'] . '://' . $new_details->domain . $new_details->path ) );
		update_option( 'home', $new_home_url );
	}

	$old_site_url    = trailingslashit( esc_url( get_option( 'siteurl' ) ) );
	$old_site_parsed = parse_url( $old_site_url );
	$old_site_host   = $old_site_parsed['host'] . ( isset( $old_site_parsed['port'] ) ? ':' . $old_site_parsed['port'] : '' );

	if ( $old_site_host === $existing_details->domain && $old_site_parsed['path'] === $existing_details->path ) {
		$new_site_url = untrailingslashit( sanitize_url( $blog_data['scheme'] . '://' . $new_details->domain . $new_details->path ) );
		update_option( 'siteurl', $new_site_url );
	}

	restore_current_blog();
	wp_redirect(
		add_query_arg(
			array(
				'update' => 'updated',
				'id'     => $id,
			),
			'site-info.php'
		)
	);
	exit;
}

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( 'updated' === $_GET['update'] ) {
		$messages[] = __( 'Site info updated.' );
	}
}

// Used in the HTML title tag.
/* translators: %s: Site title. */
$title = sprintf( __( 'Edit Site: %s' ), esc_html( $details->blogname ) );

$parent_file  = 'sites.php';
$submenu_file = 'sites.php';

require_once ABSPATH . 'wp-admin/admin-header.php';

?>

<div class="wrap">
<h1 id="edit-site"><?php echo $title; ?></h1>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( 'Visit' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( 'Dashboard' ); ?></a></p>
<?php

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-info',
	)
);

if ( ! empty( $messages ) ) {
	$notice_args = array(
		'type'        => 'success',
		'dismissible' => true,
		'id'          => 'message',
	);

	foreach ( $messages as $msg ) {
		wp_admin_notice( $msg, $notice_args );
	}
}
?>
<form method="post" action="site-info.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<?php
		// The main site of the network should not be updated on this page.
		if ( $is_main_site ) :
			?>
		<tr class="form-field">
			<th scope="row"><?php _e( 'Site Address (URL)' ); ?></th>
			<td><?php echo esc_url( $parsed_scheme . '://' . $details->domain . $details->path ); ?></td>
		</tr>
			<?php
			// For any other site, the scheme, domain, and path can all be changed.
		else :
			?>
		<tr class="form-field form-required">
			<th scope="row"><label for="url"><?php _e( 'Site Address (URL)' ); ?></label></th>
			<td><input name="blog[url]" type="text" id="url" value="<?php echo $parsed_scheme . '://' . esc_attr( $details->domain ) . esc_attr( $details->path ); ?>" /></td>
		</tr>
		<?php endif; ?>

		<tr class="form-field">
			<th scope="row"><label for="blog_registered"><?php _ex( 'Registered', 'site' ); ?></label></th>
			<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr( $details->registered ); ?>" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="blog_last_updated"><?php _e( 'Last Updated' ); ?></label></th>
			<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr( $details->last_updated ); ?>" /></td>
		</tr>
		<?php
		$attribute_fields = array( 'public' => _x( 'Public', 'site' ) );
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
			<legend class="screen-reader-text">
				<?php
				/* translators: Hidden accessibility text. */
				_e( 'Set site attributes' );
				?>
			</legend>
			<?php foreach ( $attribute_fields as $field_key => $field_label ) : ?>
				<label><input type="checkbox" name="blog[<?php echo $field_key; ?>]" value="1" <?php checked( (bool) $details->$field_key, true ); ?> <?php disabled( ! in_array( (int) $details->$field_key, array( 0, 1 ), true ) ); ?> />
				<?php echo $field_label; ?></label><br />
			<?php endforeach; ?>
			<fieldset>
			</td>
		</tr>
	</table>

	<?php
	/**
	 * Fires at the end of the site info form in network admin.
	 *
	 * @since 5.6.0
	 *
	 * @param int $id The site ID.
	 */
	do_action( 'network_site_info_form', $id );

	submit_button();
	?>
</form>

</div>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
