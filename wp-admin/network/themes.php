<?php
/**
 * Multisite themes administration panel.
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

if ( !current_user_can('manage_network_themes') )
	wp_die( __( 'You do not have sufficient permissions to manage network themes.' ) );

$wp_list_table = get_list_table('WP_MS_Themes_List_Table');

$action = $wp_list_table->current_action();

$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('enable', 'disable', 'enable-selected', 'disable-selected'), $_SERVER['REQUEST_URI']);

if ( $action ) {
	$allowed_themes = get_site_option( 'allowedthemes' );	
	switch ( $action ) {
		case 'enable':
			$allowed_themes[ $_GET['theme'] ] = true;
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_redirect( wp_get_referer() ); // @todo add_query_arg for update message
			exit;			
			break;
		case 'disable':
			unset( $allowed_themes[ $_GET['theme'] ] );
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_redirect( wp_get_referer() ); // @todo add_query_arg for update message
			exit;
			break;
		case 'enable-selected':
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				wp_redirect( wp_get_referer() );
				exit;
			}						
			foreach( (array) $themes as $theme )
				$allowed_themes[ $theme ] = true;
			update_site_option( 'allowedthemes', $allowed_themes );
			break;
		case 'disable-selected':
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				wp_redirect( wp_get_referer() );
				exit;
			}						
			foreach( (array) $themes as $theme )
				unset( $allowed_themes[ $theme ] );
			update_site_option( 'allowedthemes', $allowed_themes );
			break;
	}
}

$wp_list_table->prepare_items();
add_thickbox();

add_screen_option( 'per_page', array('label' => _x( 'Themes', 'themes per page (screen options)' )) );

add_contextual_help($current_screen,
	'<p>' . __('This screen enables and disables the inclusion of themes available to choose in the Appearance menu for each site. It does not activate or deactivate which theme a site is currently using.') . '</p>' .
	'<p>' . __('If the network admin disables a theme that is in use, it can still remain selected on that site. If another theme is chosen, the disabled theme will not appear in the site&#8217;s Appearance > Themes screen.') . '</p>' .
	'<p>' . __('Themes can be enabled on a site by site basis by the network admin on the Edit Site screen you go to via the Edit action link on the Sites screen. Only network admins are able to install or edit themes.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Themes_Screen" target="_blank">Documentation on Network Themes</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

$title = __('Themes');
$parent_file = 'themes.php';

require_once(ABSPATH . 'wp-admin/admin-header.php');

?>

<div class="wrap">
<?php screen_icon('themes'); ?>
<h2><?php echo esc_html( $title ); if ( current_user_can('install_themes') ) { ?> <a href="theme-install.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'theme'); ?></a><?php } ?></h2>

<form method="get" action="">
<?php $wp_list_table->search_box( __( 'Search Installed Themes' ), 'theme' ); ?>
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="">
<input type="hidden" name="theme_status" value="<?php echo esc_attr($status) ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr($page) ?>" />

<?php $wp_list_table->display(); ?>
</form>

</div>

<?php
include(ABSPATH . 'wp-admin/admin-footer.php');
