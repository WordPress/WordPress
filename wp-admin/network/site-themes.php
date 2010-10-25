<?php

/**
 * Edit Site Themes Administration Screen
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
	wp_die(__('You do not have sufficient permissions to edit this site.'));

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( $details->site_id != $wpdb->siteid )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

if ( isset($_REQUEST['action']) && 'update-site' == $_REQUEST['action'] ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	$allowedthemes = array();
	if ( isset($_POST['theme']) && is_array( $_POST['theme'] ) ) {
		foreach ( $_POST['theme'] as $theme => $val ) {
			if ( 'on' == $val )
				$allowedthemes[$theme] = true;
		}
	}
	update_option( 'allowedthemes',  $allowedthemes );

	restore_current_blog();
	wp_redirect( add_query_arg( array( 'update' => 'updated', 'id' => $id ), 'site-themes.php') );
}

if ( isset($_GET['update']) ) {
	$messages = array();
	if ( 'updated' == $_GET['update'] )
		$messages[] = __('Site users updated.');
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
<form method="post" action="site-themes.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
<?php
$themes = get_themes();
$blog_allowed_themes = wpmu_get_blog_allowedthemes( $id );
$allowed_themes = get_site_option( 'allowedthemes' );

if ( ! $allowed_themes )
	$allowed_themes = array_keys( $themes );

$out = '';
foreach ( $themes as $key => $theme ) {
	$theme_key = esc_html( $theme['Stylesheet'] );
	if ( ! isset( $allowed_themes[$theme_key] ) ) {
		$checked = isset( $blog_allowed_themes[ $theme_key ] ) ? 'checked="checked"' : '';
		$out .= '<tr class="form-field form-required">
				<th title="' . esc_attr( $theme["Description"] ).'" scope="row">' . esc_html( $key ) . '</th>
				<td><label><input name="theme[' . esc_attr( $theme_key ) . ']" type="checkbox" style="width:20px;" value="on" '.$checked.'/> ' . __( 'Active' ) . '</label></td>
			</tr>';
	}
}

if ( $out != '' ) {
?>
<p class="description"><?php _e( 'Activate the themename of an existing theme and hit "Update Options" to allow the theme for this site.' ) ?></p>
<table class="form-table">
<?php echo $out; ?>
</table>
<?php
submit_button();
} else {
	_e('All themes are allowed.');
}
?>
</form>

</div>
<?php
require('../admin-footer.php');