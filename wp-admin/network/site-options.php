<?php
/**
 * Edit Site Options Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can('manage_sites') )
	wp_die(__('You do not have sufficient permissions to edit this site.'));

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( $details->site_id != $wpdb->siteid )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

if ( isset($_REQUEST['action']) && 'update-site' == $_REQUEST['action'] && is_array( $_POST['option'] ) ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	$c = 1;
	$count = count( $_POST['option'] );
	$skip_options = array( 'allowedthemes' ); // Don't update these options since they are handled elsewhere in the form.
	foreach ( (array) $_POST['option'] as $key => $val ) {
		if ( $key === 0 || is_array( $val ) || in_array($key, $skip_options) )
			continue; // Avoids "0 is a protected WP option and may not be modified" error when edit blog options
		if ( $c == $count )
			update_option( $key, stripslashes( $val ) );
		else
			update_option( $key, stripslashes( $val ), false ); // no need to refresh blog details yet
		$c++;
	}

	restore_current_blog();
	wp_redirect( add_query_arg( array( 'update' => 'updated', 'id' => $id ), 'site-options.php') );
}

if ( isset($_GET['update']) ) {
	$messages = array();
	if ( 'updated' == $_GET['update'] )
		$messages[] = __('Site options updated.');
}

$title = sprintf( __('Edit Site: %s'), get_blogaddress_by_id($id));
$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require('../admin-header.php');

?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="edit-site"><?php echo $title ?></h2>
<h3 class="nav-tab-wrapper">
<?php
$tabs = array( 'site-info' => array( 'label' => __('Info'), 'url' => 'site-info.php'),  'site-options' => array( 'label' => __('Options'), 'url' => 'site-options.php'),
			  'site-users' => array( 'label' => __('Users'), 'url' => 'site-users.php'),  'site-themes' => array( 'label' => __('Themes'), 'url' => 'site-themes.php'));
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' .  esc_html( $tab['label'] ) . '</a>';
}
?>
</h3>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg )
		echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
} ?>
<form method="post" action="site-options.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<?php
		$blog_prefix = $wpdb->get_blog_prefix( $id );
		$options = $wpdb->get_results( "SELECT * FROM {$blog_prefix}options WHERE option_name NOT LIKE '\_%' AND option_name NOT LIKE '%user_roles'" );
		foreach ( $options as $option ) {
			if ( $option->option_name == 'default_role' )
				$editblog_default_role = $option->option_value;
			$disabled = false;
			$class = 'all-options';
			if ( is_serialized( $option->option_value ) ) {
				if ( is_serialized_string( $option->option_value ) ) {
					$option->option_value = esc_html( maybe_unserialize( $option->option_value ), 'single' );
				} else {
					$option->option_value = 'SERIALIZED DATA';
					$disabled = true;
					$class = 'all-options disabled';
				}
			}
			if ( strpos( $option->option_value, "\n" ) !== false ) {
			?>
				<tr class="form-field">
					<th scope="row"><?php echo ucwords( str_replace( "_", " ", $option->option_name ) ) ?></th>
					<td><textarea class="<?php echo $class; ?>" rows="5" cols="40" name="option[<?php echo esc_attr( $option->option_name ) ?>]" id="<?php echo esc_attr( $option->option_name ) ?>"<?php disabled( $disabled ) ?>><?php echo esc_textarea( $option->option_value ) ?></textarea></td>
				</tr>
			<?php
			} else {
			?>
				<tr class="form-field">
					<th scope="row"><?php echo esc_html( ucwords( str_replace( "_", " ", $option->option_name ) ) ); ?></th>
					<?php if ( $is_main_site && in_array( $option->option_name, array( 'siteurl', 'home' ) ) ) { ?>
					<td><code><?php echo esc_html( $option->option_value ) ?></code></td>
					<?php } else { ?>
					<td><input class="<?php echo $class; ?>" name="option[<?php echo esc_attr( $option->option_name ) ?>]" type="text" id="<?php echo esc_attr( $option->option_name ) ?>" value="<?php echo esc_attr( $option->option_value ) ?>" size="40" <?php disabled( $disabled ) ?> /></td>
					<?php } ?>
				</tr>
			<?php
			}
		} // End foreach
		?>
	</table>
	<?php submit_button(); ?>
</form>

</div>
<?php
require('../admin-footer.php');