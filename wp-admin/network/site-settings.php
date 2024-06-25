<?php
/**
 * Edit Site Settings Administration Screen
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

$is_main_site = is_main_site( $id );

if ( isset( $_REQUEST['action'] ) && 'update-site' === $_REQUEST['action'] && is_array( $_POST['option'] ) ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	$skip_options = array( 'allowedthemes' ); // Don't update these options since they are handled elsewhere in the form.
	foreach ( (array) $_POST['option'] as $key => $val ) {
		$key = wp_unslash( $key );
		$val = wp_unslash( $val );
		if ( 0 === $key || is_array( $val ) || in_array( $key, $skip_options, true ) ) {
			continue; // Avoids "0 is a protected WP option and may not be modified" error when editing blog options.
		}
		update_option( $key, $val );
	}

	/**
	 * Fires after the site options are updated.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 Added `$id` parameter.
	 *
	 * @param int $id The ID of the site being updated.
	 */
	do_action( 'wpmu_update_blog_options', $id );

	restore_current_blog();
	wp_redirect(
		add_query_arg(
			array(
				'update' => 'updated',
				'id'     => $id,
			),
			'site-settings.php'
		)
	);
	exit;
}

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( 'updated' === $_GET['update'] ) {
		$messages[] = __( 'Site options updated.' );
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
		'selected' => 'site-settings',
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
<form method="post" action="site-settings.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<?php
		$blog_prefix = $wpdb->get_blog_prefix( $id );
		$sql         = "SELECT * FROM {$blog_prefix}options
			WHERE option_name NOT LIKE %s
			AND option_name NOT LIKE %s";
		$query       = $wpdb->prepare(
			$sql,
			$wpdb->esc_like( '_' ) . '%',
			'%' . $wpdb->esc_like( 'user_roles' )
		);
		$options     = $wpdb->get_results( $query );

		foreach ( $options as $option ) {
			if ( 'default_role' === $option->option_name ) {
				$editblog_default_role = $option->option_value;
			}

			$disabled = false;
			$class    = 'all-options';

			if ( is_serialized( $option->option_value ) ) {
				if ( is_serialized_string( $option->option_value ) ) {
					$option->option_value = esc_html( maybe_unserialize( $option->option_value ) );
				} else {
					$option->option_value = 'SERIALIZED DATA';
					$disabled             = true;
					$class                = 'all-options disabled';
				}
			}

			if ( str_contains( $option->option_value, "\n" ) ) {
				?>
				<tr class="form-field">
					<th scope="row"><label for="<?php echo esc_attr( $option->option_name ); ?>" class="code"><?php echo esc_html( $option->option_name ); ?></label></th>
					<td><textarea class="<?php echo $class; ?>" rows="5" cols="40" name="option[<?php echo esc_attr( $option->option_name ); ?>]" id="<?php echo esc_attr( $option->option_name ); ?>"<?php disabled( $disabled ); ?>><?php echo esc_textarea( $option->option_value ); ?></textarea></td>
				</tr>
				<?php
			} else {
				?>
				<tr class="form-field">
					<th scope="row"><label for="<?php echo esc_attr( $option->option_name ); ?>" class="code"><?php echo esc_html( $option->option_name ); ?></label></th>
					<?php if ( $is_main_site && in_array( $option->option_name, array( 'siteurl', 'home' ), true ) ) { ?>
					<td><code><?php echo esc_html( $option->option_value ); ?></code></td>
					<?php } else { ?>
					<td><input class="<?php echo $class; ?>" name="option[<?php echo esc_attr( $option->option_name ); ?>]" type="text" id="<?php echo esc_attr( $option->option_name ); ?>" value="<?php echo esc_attr( $option->option_value ); ?>" size="40" <?php disabled( $disabled ); ?> /></td>
					<?php } ?>
				</tr>
				<?php
			}
		} // End foreach.

		/**
		 * Fires at the end of the Edit Site form, before the submit button.
		 *
		 * @since 3.0.0
		 *
		 * @param int $id Site ID.
		 */
		do_action( 'wpmueditblogaction', $id );
		?>
	</table>
	<?php submit_button(); ?>
</form>

</div>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
