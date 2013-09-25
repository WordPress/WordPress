<?php
/**
 * Install theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( !defined( 'IFRAME_REQUEST' ) && isset( $_GET['tab'] ) && ( 'theme-information' == $_GET['tab'] ) )
	define( 'IFRAME_REQUEST', true );

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can('install_themes') )
	wp_die( __( 'You do not have sufficient permissions to install themes on this site.' ) );

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'theme-install.php' ) );
	exit();
}

$wp_list_table = _get_list_table('WP_Theme_Install_List_Table');
$pagenum = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();

$title = __('Install Themes');
$parent_file = 'themes.php';
if ( !is_network_admin() )
	$submenu_file = 'themes.php';

wp_enqueue_script( 'theme-install' );
wp_enqueue_script( 'theme' );

$body_id = $tab;

do_action('install_themes_pre_' . $tab); //Used to override the general interface, Eg, install or theme information.

$help_overview =
	'<p>' . sprintf(__('You can find additional themes for your site by using the Theme Browser/Installer on this screen, which will display themes from the <a href="%s" target="_blank">WordPress.org Theme Directory</a>. These themes are designed and developed by third parties, are available free of charge, and are compatible with the license WordPress uses.'), 'http://wordpress.org/themes/') . '</p>' .
	'<p>' . __('You can Search for themes by keyword, author, or tag, or can get more specific and search by criteria listed in the feature filter. Alternately, you can browse the themes that are Featured, Newest, or Recently Updated. When you find a theme you like, you can preview it or install it.') . '</p>' .
	'<p>' . __('You can Upload a theme manually if you have already downloaded its ZIP archive onto your computer (make sure it is from a trusted and original source). You can also do it the old-fashioned way and copy a downloaded theme&#8217;s folder via FTP into your <code>/wp-content/themes</code> directory.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => $help_overview
) );

$help_installing =
	'<p>' . __('Once you have generated a list of themes, you can preview and install any of them. Click on the thumbnail of the theme you&#8217;re interested in previewing. It will open up in a full-screen Preview page to give you a better idea of how that theme will look.') . '</p>' .
	'<p>' . __('To install the theme so you can preview it with your site&#8217;s content and customize its theme options, click the "Install" button at the top of the left-hand pane. The theme files will be downloaded to your website automatically. When this is complete, the theme is now available for activation, which you can do by clicking the "Activate" link, or by navigating to your Manage Themes screen and clicking the "Live Preview" link under any installed theme&#8217;s thumbnail image.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'installing',
	'title'   => __('Previewing and Installing'),
	'content' => $help_installing
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Using_Themes#Adding_New_Themes" target="_blank">Documentation on Adding New Themes</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include(ABSPATH . 'wp-admin/admin-header.php');
?>
<div class="wrap">
<?php
screen_icon();

if ( is_network_admin() ) : ?>
<h2><?php echo esc_html( $title ); ?></h2>
<?php else : ?>
<h2 class="nav-tab-wrapper"><a href="themes.php" class="nav-tab"><?php echo esc_html_x('Manage Themes', 'theme'); ?></a><a href="theme-install.php" class="nav-tab nav-tab-active"><?php echo esc_html( $title ); ?></a></h2>

<?php
endif;

$wp_list_table->views(); ?>

<br class="clear" />
<?php do_action('install_themes_' . $tab, $paged); ?>
</div>
<?php
include(ABSPATH . 'wp-admin/admin-footer.php');
