<?php
/**
 * Install theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can('install_themes') )
	wp_die(__('You do not have sufficient permissions to install themes on this site.'));

include(ABSPATH . 'wp-admin/includes/theme-install.php');

$title = __('Install Themes');
$parent_file = 'themes.php';
$submenu_file = 'themes.php';

wp_reset_vars( array('tab', 'paged') );
wp_enqueue_style( 'theme-install' );
wp_enqueue_script( 'theme-install' );

add_thickbox();
wp_enqueue_script( 'theme-preview' );

//These are the tabs which are shown on the page,
$tabs = array();
$tabs['dashboard'] = __('Search');
if ( 'search' == $tab )
	$tabs['search']	= __('Search Results');
$tabs['upload'] = __('Upload');
$tabs['featured'] = _x('Featured','Theme Installer');
//$tabs['popular']  = _x('Popular','Theme Installer');
$tabs['new']      = _x('Newest','Theme Installer');
$tabs['updated']  = _x('Recently Updated','Theme Installer');

$nonmenu_tabs = array('theme-information'); //Valid actions to perform which do not have a Menu item.

$tabs = apply_filters('install_themes_tabs', $tabs );
$nonmenu_tabs = apply_filters('install_themes_nonmenu_tabs', $nonmenu_tabs);

//If a non-valid menu tab has been selected, And its not a non-menu action.
if ( empty($tab) || ( ! isset($tabs[ $tab ]) && ! in_array($tab, (array)$nonmenu_tabs) ) ) {
	$tab_actions = array_keys($tabs);
	$tab = $tab_actions[0];
}
if ( empty($paged) )
	$paged = 1;

$body_id = $tab;

do_action('install_themes_pre_' . $tab); //Used to override the general interface, Eg, install or theme information.

$help = '<p>' . __('You can find additional themes for your site by using the Theme Browser/Installer on this screen, which will display themes from the WordPress.org theme repository. These themes are designed and developed by third parties, are available free of charge, and are licensed under the GNU General Public License, version 2, just like WordPress.') . '</p>';
$help .= '<p>' . __('You can Search for themes by keyword, author, or tag, or can get more specific and search by criteria listed in the feature filter. Alternately, you can browse the themes that are Featured, Newest, or Recently Updated. When you find a theme you like, you can preview it or install it.') . '</p>';
$help .= '<p>' . __('You can Upload a theme manually if you have already downloaded its ZIP archive onto your computer (make sure it is from a trusted and original source). You can also do it the old-fashioned way and copy a downloaded theme&#8217;s folder via FTP into your wp-content/themes directory.') . '</p>';
$help .= '<p>' . __('<strong>For more information:</strong>') . '</p>';
$help .= '<p>' . sprintf(__('<a href="%s">Documentation on Using Themes</a>'), 'http://codex.wordpress.org/Using_Themes#Adding_New_Themes') . '</p>';
$help .= '<p>' . sprintf(__('<a href="%s">Support Forums</a>'), 'http://wordpress.org/support/') . '</p>';
add_contextual_help($current_screen, $help);

include('./admin-header.php');
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><a href="themes.php" class="nav-tab"><?php echo esc_html_x('Manage Themes', 'theme'); ?></a><a href="theme-install.php" class="nav-tab nav-tab-active"><?php echo esc_html( $title ); ?></a></h2>

	<ul class="subsubsub">
<?php
$display_tabs = array();
foreach ( (array)$tabs as $action => $text ) {
	$sep = ( end($tabs) != $text ) ? ' | ' : '';
	$class = ( $action == $tab ) ? ' class="current"' : '';
	$href = admin_url('theme-install.php?tab='. $action);
	echo "\t\t<li><a href='$href'$class>$text</a>$sep</li>\n";
}
?>
	</ul>
	<br class="clear" />
	<?php do_action('install_themes_' . $tab, $paged); ?>
</div>
<?php
include('./admin-footer.php');
