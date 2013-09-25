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

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'You do not have sufficient permissions to edit this site.' ) );

	get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __('Overview'),
		'content' =>
			'<p>' . __('The menu is for editing information specific to individual sites, particularly if the admin area of a site is unavailable.') . '</p>' .
			'<p>' . __('<strong>Info</strong> - The domain and path are rarely edited as this can cause the site to not work properly. The Registered date and Last Updated date are displayed. Network admins can mark a site as archived, spam, deleted and mature, to remove from public listings or disable.') . '</p>' .
			'<p>' . __('<strong>Users</strong> - This displays the users associated with this site. You can also change their role, reset their password, or remove them from the site. Removing the user from the site does not remove the user from the network.') . '</p>' .
			'<p>' . sprintf( __('<strong>Themes</strong> - This area shows themes that are not already enabled across the network. Enabling a theme in this menu makes it accessible to this site. It does not activate the theme, but allows it to show in the site&#8217;s Appearance menu. To enable a theme for the entire network, see the <a href="%s">Network Themes</a> screen.' ), network_admin_url( 'themes.php' ) ) . '</p>' .
			'<p>' . __('<strong>Settings</strong> - This page shows a list of all settings associated with this site. Some are created by WordPress and others are created by plugins you activate. Note that some fields are grayed out and say Serialized Data. You cannot modify these values due to the way the setting is stored in the database.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Sites_Screen" target="_blank">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( !can_edit_network( $details->site_id ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

if ( isset($_REQUEST['action']) && 'update-site' == $_REQUEST['action'] ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	if ( isset( $_POST['update_home_url'] ) && $_POST['update_home_url'] == 'update' ) {
		$blog_address = esc_url_raw( $_POST['blog']['domain'] . $_POST['blog']['path'] );
		if ( get_option( 'siteurl' ) != $blog_address )
			update_option( 'siteurl', $blog_address );

		if ( get_option( 'home' ) != $blog_address )
			update_option( 'home', $blog_address );
	}

	// rewrite rules can't be flushed during switch to blog
	delete_option( 'rewrite_rules' );

	// update blogs table
	$blog_data = wp_unslash( $_POST['blog'] );
	$existing_details = get_blog_details( $id, false );
	$blog_data_checkboxes = array( 'public', 'archived', 'spam', 'mature', 'deleted' );
	foreach ( $blog_data_checkboxes as $c ) {
		if ( ! in_array( $existing_details->$c, array( 0, 1 ) ) )
			$blog_data[ $c ] = $existing_details->$c;
		else
			$blog_data[ $c ] = isset( $_POST['blog'][ $c ] ) ? 1 : 0;
	}
	update_blog_details( $id, $blog_data );

	restore_current_blog();
	wp_redirect( add_query_arg( array( 'update' => 'updated', 'id' => $id ), 'site-info.php') );
	exit;
}

if ( isset($_GET['update']) ) {
	$messages = array();
	if ( 'updated' == $_GET['update'] )
		$messages[] = __('Site info updated.');
}

$site_url_no_http = preg_replace( '#^http(s)?://#', '', get_blogaddress_by_id( $id ) );
$title_site_url_linked = sprintf( __('Edit Site: <a href="%1$s">%2$s</a>'), get_blogaddress_by_id( $id ), $site_url_no_http );
$title = sprintf( __('Edit Site: %s'), $site_url_no_http );

$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="edit-site"><?php echo $title_site_url_linked ?></h2>
<h3 class="nav-tab-wrapper">
<?php
$tabs = array(
	'site-info'     => array( 'label' => __( 'Info' ),     'url' => 'site-info.php'     ),
	'site-users'    => array( 'label' => __( 'Users' ),    'url' => 'site-users.php'    ),
	'site-themes'   => array( 'label' => __( 'Themes' ),   'url' => 'site-themes.php'   ),
	'site-settings' => array( 'label' => __( 'Settings' ), 'url' => 'site-settings.php' ),
);
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' . esc_html( $tab['label'] ) . '</a>';
}
?>
</h3>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg )
		echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
} ?>
<form method="post" action="site-info.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Domain' ) ?></th>
			<?php
			$protocol = is_ssl() ? 'https://' : 'http://';
			if ( $is_main_site ) { ?>
			<td><code><?php echo $protocol; echo esc_attr( $details->domain ) ?></code></td>
			<?php } else { ?>
			<td><?php echo $protocol; ?><input name="blog[domain]" type="text" id="domain" value="<?php echo esc_attr( $details->domain ) ?>" size="33" /></td>
			<?php } ?>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><?php _e( 'Path' ) ?></th>
			<?php if ( $is_main_site ) { ?>
			<td><code><?php echo esc_attr( $details->path ) ?></code></td>
			<?php
			} else {
				switch_to_blog( $id );
			?>
			<td><input name="blog[path]" type="text" id="path" value="<?php echo esc_attr( $details->path ) ?>" size="40" style='margin-bottom:5px;' />
			<br /><input type="checkbox" style="width:20px;" name="update_home_url" value="update" <?php if ( get_option( 'siteurl' ) == untrailingslashit( get_blogaddress_by_id ($id ) ) || get_option( 'home' ) == untrailingslashit( get_blogaddress_by_id( $id ) ) ) echo 'checked="checked"'; ?> /> <?php _e( 'Update <code>siteurl</code> and <code>home</code> as well.' ); ?></td>
			<?php
				restore_current_blog();
			} ?>
		</tr>
		<tr class="form-field">
			<th scope="row"><?php _ex( 'Registered', 'site' ) ?></th>
			<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr( $details->registered ) ?>" size="40" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><?php _e( 'Last Updated' ); ?></th>
			<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr( $details->last_updated ) ?>" size="40" /></td>
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
			<?php foreach ( $attribute_fields as $field_key => $field_label ) : ?>
				<label><input type="checkbox" name="blog[<?php echo $field_key; ?>]" value="1" <?php checked( (bool) $details->$field_key, true ); disabled( ! in_array( $details->$field_key, array( 0, 1 ) ) ); ?> />
				<?php echo $field_label; ?></label><br/>
			<?php endforeach; ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>

</div>
<?php
require( ABSPATH . 'wp-admin/admin-footer.php' );
