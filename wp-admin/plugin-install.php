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

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can('install_plugins') )
	wp_die(__('You do not have sufficient permissions to install plugins on this site.'));

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'plugin-install.php' ) );
	exit();
}

$wp_list_table = _get_list_table('WP_Plugin_Install_List_Table');
$pagenum = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();

$title = __('Install Plugins');
$parent_file = 'plugins.php';

wp_enqueue_script( 'plugin-install' );
if ( 'plugin-information' != $tab )
	add_thickbox();

$body_id = $tab;

do_action('install_plugins_pre_' . $tab); //Used to override the general interface, Eg, install or plugin information.

get_current_screen()->add_help_tab( array(
'id'		=> 'overview',
'title'		=> __('Overview'),
'content'	=>
	'<p>' . sprintf(__('Plugins hook into WordPress to extend its functionality with custom features. Plugins are developed independently from the core WordPress application by thousands of developers all over the world. All plugins in the official <a href="%s" target="_blank">WordPress.org Plugin Directory</a> are compatible with the license WordPress uses. You can find new plugins to install by searching or browsing the Directory right here in your own Plugins section.'), 'http://wordpress.org/plugins/') . '</p>'
) );
get_current_screen()->add_help_tab( array(
'id'		=> 'adding-plugins',
'title'		=> __('Adding Plugins'),
'content'	=>
	'<p>' . __('If you know what you&#8217;re looking for, Search is your best bet. The Search screen has options to search the WordPress.org Plugin Directory for a particular Term, Author, or Tag. You can also search the directory by selecting popular tags. Tags in larger type mean more plugins have been labeled with that tag.') . '</p>' .
	'<p>' . __('If you just want to get an idea of what&#8217;s available, you can browse Featured, Popular, and Newest plugins by using the links in the upper left of the screen. These sections rotate regularly.') . '</p>' .
	'<p>' . __('You can also browse a user&#8217;s favorite plugins, by using the Favorites link in the upper left of the screen and entering their WordPress.org username.') . '</p>' .
	'<p>' . __('If you want to install a plugin that you&#8217;ve downloaded elsewhere, click the Upload link in the upper left. You will be prompted to upload the .zip package, and once uploaded, you can activate the new plugin.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Plugins_Add_New_Screen" target="_blank">Documentation on Installing Plugins</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include(ABSPATH . 'wp-admin/admin-header.php');
?>
<div class="wrap">
<?php screen_icon( 'plugins' ); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php $wp_list_table->views(); ?>

<br class="clear" />
<?php do_action('install_plugins_' . $tab, $paged); ?>
</div>
<?php
include(ABSPATH . 'wp-admin/admin-footer.php');
