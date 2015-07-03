<?php
/**
 * Install plugin administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */
// TODO route this pages via a specific iframe handler instead of the do_action below
if ( !defined( 'IFRAME_REQUEST' ) && isset( $_GET['tab'] ) && ( 'plugin-information' == $_GET['tab'] ) )
	define( 'IFRAME_REQUEST', true );

/**
 * WordPress Administration Bootstrap.
 */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can('install_plugins') )
	wp_die(__('You do not have sufficient permissions to install plugins on this site.'));

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'plugin-install.php' ) );
	exit();
}

$wp_list_table = _get_list_table('WP_Plugin_Install_List_Table');
$pagenum = $wp_list_table->get_pagenum();

if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
	$location = remove_query_arg( '_wp_http_referer', wp_unslash( $_SERVER['REQUEST_URI'] ) );

	if ( ! empty( $_REQUEST['paged'] ) ) {
		$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
	}

	wp_redirect( $location );
	exit;
}

$wp_list_table->prepare_items();

$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	wp_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}

$title = __( 'Add Plugins' );
$parent_file = 'plugins.php';

wp_enqueue_script( 'plugin-install' );
if ( 'plugin-information' != $tab )
	add_thickbox();

$body_id = $tab;

wp_enqueue_script( 'updates' );

/**
 * Fires before each tab on the Install Plugins screen is loaded.
 *
 * The dynamic portion of the action hook, `$tab`, allows for targeting
 * individual tabs, for instance 'install_plugins_pre_plugin-information'.
 *
 * @since 2.7.0
 */
do_action( "install_plugins_pre_$tab" );

get_current_screen()->add_help_tab( array(
'id'		=> 'overview',
'title'		=> __('Overview'),
'content'	=>
	'<p>' . sprintf(__('Plugins hook into WordPress to extend its functionality with custom features. Plugins are developed independently from the core WordPress application by thousands of developers all over the world. All plugins in the official <a href="%s" target="_blank">WordPress.org Plugin Directory</a> are compatible with the license WordPress uses. You can find new plugins to install by searching or browsing the Directory right here in your own Plugins section.'), 'https://wordpress.org/plugins/') . '</p>'
) );
get_current_screen()->add_help_tab( array(
'id'		=> 'adding-plugins',
'title'		=> __('Adding Plugins'),
'content'	=>
	'<p>' . __('If you know what you&#8217;re looking for, Search is your best bet. The Search screen has options to search the WordPress.org Plugin Directory for a particular Term, Author, or Tag. You can also search the directory by selecting popular tags. Tags in larger type mean more plugins have been labeled with that tag.') . '</p>' .
	'<p>' . __('If you just want to get an idea of what&#8217;s available, you can browse Featured and Popular plugins by using the links in the upper left of the screen. These sections rotate regularly.') . '</p>' .
	'<p>' . __('You can also browse a user&#8217;s favorite plugins, by using the Favorites link in the upper left of the screen and entering their WordPress.org username.') . '</p>' .
	'<p>' . __('If you want to install a plugin that you&#8217;ve downloaded elsewhere, click the Upload link in the upper left. You will be prompted to upload the .zip package, and once uploaded, you can activate the new plugin.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Plugins_Add_New_Screen" target="_blank">Documentation on Installing Plugins</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

/**
 * WordPress Administration Template Header.
 */
include(ABSPATH . 'wp-admin/admin-header.php');
?>
<div class="wrap">
<h1>
	<?php
	echo esc_html( $title );
	if ( ! empty( $tabs['upload'] ) && current_user_can( 'upload_plugins' ) ) {
		if ( $tab === 'upload' ) {
			$href = self_admin_url( 'plugin-install.php' );
			$text = _x( 'Browse', 'plugins' );
		} else {
			$href = self_admin_url( 'plugin-install.php?tab=upload' );
			$text = __( 'Upload Plugin' );
		}
		echo ' <a href="' . $href . '" class="upload page-title-action">' . $text . '</a>';
	}
	?>
</h1>

<?php
if ( $tab !== 'upload' ) {
	$wp_list_table->views();
	echo '<br class="clear" />';
}

/**
 * Fires after the plugins list table in each tab of the Install Plugins screen.
 *
 * The dynamic portion of the action hook, `$tab`, allows for targeting
 * individual tabs, for instance 'install_plugins_plugin-information'.
 *
 * @since 2.7.0
 *
 * @param int $paged The current page number of the plugins list table.
 */
do_action( "install_plugins_$tab", $paged ); ?>
</div>

<?php
wp_print_request_filesystem_credentials_modal();

/**
 * WordPress Administration Template Footer.
 */
include(ABSPATH . 'wp-admin/admin-footer.php');
