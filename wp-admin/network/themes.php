<?php
/**
 * Multisite themes administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

require_once( './admin.php' );

$wp_list_table = get_list_table('WP_MS_Themes_Table');
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

add_screen_option( 'per_page', array('label' => _x( 'Themes', 'themes per page (screen options)' ), 'default' => 999) );

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