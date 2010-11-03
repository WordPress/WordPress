<?php
/**
 * Multisite themes administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

require_once( './admin.php' );

$wp_list_table = get_list_table('WP_List_Table_MS_Themes');
$wp_list_table->check_permissions();

$action = $wp_list_table->current_action();

$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('error', 'deleted', 'activate', 'activate-multi', 'deactivate', 'deactivate-multi', '_error_nonce'), $_SERVER['REQUEST_URI']);

if ( $action ) {
	$allowed_themes = get_site_option( 'allowedthemes' );	
	switch ( $action ) {
		case 'network-enable':
			$allowed_themes[ $_GET['theme'] ] = 1;
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_redirect( wp_get_referer() ); // @todo add_query_arg for update message
			exit;			
			break;
		case 'network-disable':
			unset( $allowed_themes[ $_GET['theme'] ] );
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_redirect( wp_get_referer() ); // @todo add_query_arg for update message
			exit;
			break;
		case 'network-enable-selected':
			check_admin_referer('bulk-plugins');

			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				wp_redirect( wp_get_referer() );
				exit;
			}						
			foreach( (array) $themes as $theme )
				$allowed_themes[ $theme ] = 1;
			update_site_option( 'allowedthemes', $allowed_themes );
			break;
			case 'network-disable-selected':
				check_admin_referer('bulk-plugins');

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

add_screen_option( 'per_page', array('label' => _x( 'Themes', 'themes per page (screen options)' ), 'default' => 999) );

add_contextual_help($current_screen, 
	'<p>' . __('This screen enables and disables the inclusion of themes available to choose in the Appearance menu for each site. It does not activate or deactivate which theme a site is currently using.') . '</p>' . 
	'<p>' . __('If the network admin disables a theme that is in use, it can still remain selected on that site. If another theme is chosen, the disabled theme will not appear in the site&#8217;s Appearance > Themes screen.') . '</p>' . 
	'<p>' . __('Themes can be enabled on a site by site basis by the network admin on the Edit Site screen you go to via the Edit action link on the Sites screen.') . '</p>' . 
	'<p><strong>' . __('For more information:') . '</strong></p>' . 
	'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Themes_SubPanel" target="_blank">Documentation on Network Themes</a>') . '</p>' . 
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' 
);

$title = __('Themes');
$parent_file = 'themes.php';

require_once(ABSPATH . 'wp-admin/admin-header.php');

?>

<div class="wrap">
<?php screen_icon('themes'); ?>
<h2><?php echo esc_html( $title ); if ( current_user_can('install_themes') ) { ?> <a href="theme-install.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'theme'); ?></a><?php } ?></h2>
<p><?php _e( 'Themes must be enabled for your network before they will be available to individual sites.' ) ?></p>

<form method="get" action="">
<p class="search-box">
	<label class="screen-reader-text" for="theme-search-input"><?php _e( 'Search Themes' ); ?>:</label>
	<input type="text" id="theme-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<?php submit_button( __( 'Search Installed Themes' ), 'button', '', false ); ?>
</p>
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