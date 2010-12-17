<?php
/**
 * Edit Site Themes Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

$menu_perms = get_site_option( 'menu_items', array() );

if ( empty( $menu_perms['themes'] ) && ! is_super_admin() )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'You do not have sufficient permissions to manage themes for this site.' ) );

add_contextual_help($current_screen,
	'<p>' . __('The menu is for editing information specific to individual sites, particularly if the admin area of a site is unavailable.') . '</p>' .
	'<p>' . __('<strong>Themes</strong> - This area shows themes that are not already enabled across the network. Enabling a theme in this menu makes it accessible to this site. It does not activate the theme, but allows it to show in the site&#8217;s Appearance menu.') . '</p>' .
	'<p>' . sprintf( __( 'To enable a theme for the entire network, see the <a href="%s">Network Themes</a> screen.' ), network_admin_url( 'themes.php' ) ) . '</p>' .
	'<p>' . __('See the contextual help on the next tab.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Options_SubPanel" target="_blank">Network Options Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

$wp_list_table = get_list_table('WP_MS_Themes_List_Table');

$action = $wp_list_table->current_action();

$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('enable', 'disable', 'enable-selected', 'disable-selected'), $_SERVER['REQUEST_URI']);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$wp_list_table->prepare_items();

$details = get_blog_details( $id );
if ( !can_edit_network( $details->site_id ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

if ( $action ) {
	switch_to_blog( $id );
	$allowed_themes = get_option( 'allowedthemes' );

	switch ( $action ) {
		case 'enable':
			$theme = $_GET['theme'];
			$update = 'enabled';
			if ( !$allowed_themes )
				$allowed_themes = array( $theme => true );
			else
				$allowed_themes[$theme] = true;
			break;
		case 'disable':
			$theme = $_GET['theme'];
			$update = 'disabled';
			if ( !$allowed_themes )
				$allowed_themes = array();
			else
				unset( $allowed_themes[$theme] );
			break;
		case 'enable-selected':
			if ( isset( $_POST['checked'] ) ) {
				$update = 'enable';
				$themes = (array) $_POST['checked'];
				foreach( (array) $themes as $theme )
					$allowed_themes[ $theme ] = true;
			} else {
				$update = 'error';
			}
			break;
		case 'disable-selected':
			if ( isset( $_POST['checked'] ) ) {
				$update = 'disable';
				$themes = (array) $_POST['checked'];
				foreach( (array) $themes as $theme )
					unset( $allowed_themes[ $theme ] );
			} else {
				$update = 'error';
			}
			break;
	}
	
	update_option( 'allowedthemes', $allowed_themes );
	restore_current_blog();
	
	wp_redirect( add_query_arg( 'update', $update, wp_get_referer() ) );
	exit;	
}

if ( isset( $_GET['action'] ) && 'update-site' == $_GET['action'] ) {
	wp_redirect( wp_get_referer() );
	exit();
}

add_thickbox();
add_screen_option( 'per_page', array( 'label' => _x( 'Themes', 'themes per page (screen options)' ) ) );

$title = sprintf( __('Edit Site: %s'), get_blogaddress_by_id($id));
$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require('../admin-header.php'); ?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="edit-site"><?php echo $title ?></h2>
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
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' .  esc_html( $tab['label'] ) . '</a>';
}
?>
</h3><?php

if ( isset( $_GET['update'] ) ) {
	switch ( $_GET['update'] ) {
	case 'enabled':
		echo '<div id="message" class="updated"><p>' . __( 'Theme enabled.' ) . '</p></div>';
		break;
	case 'disabled':
		echo '<div id="message" class="updated"><p>' . __( 'Theme disabled.' ) . '</p></div>';
		break;
	case 'error':
		echo '<div id="message" class="error"><p>' . __( 'No theme selected.' ) . '</p></div>';
		break;
	}
} ?>

<p><?php _e( 'Network enabled themes are not shown on this screen.' ) ?></p>

<form method="get" action="">
<?php $wp_list_table->search_box( __( 'Search Installed Themes' ), 'theme' ); ?>
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="site-themes.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />

<?php $wp_list_table->display(); ?>

</form>

</div>
<?php include(ABSPATH . 'wp-admin/admin-footer.php'); ?>
