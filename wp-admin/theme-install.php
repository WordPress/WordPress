<?php
/**
 * Install theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

require_once( './includes/default-list-tables.php' );

$wp_list_table = new WP_Theme_Install_Table;
$wp_list_table->check_permissions();
$wp_list_table->prepare_items();

$title = __('Install Themes');
$parent_file = 'themes.php';
$submenu_file = 'themes.php';

wp_enqueue_style( 'theme-install' );
wp_enqueue_script( 'theme-install' );

add_thickbox();
wp_enqueue_script( 'theme-preview' );

$body_id = $tab;

do_action('install_themes_pre_' . $tab); //Used to override the general interface, Eg, install or theme information.

$help = '<p>' . sprintf(__('You can find additional themes for your site by using the Theme Browser/Installer on this screen, which will display themes from the <a href="%s" target="_blank">WordPress.org Theme Directory</a>. These themes are designed and developed by third parties, are available free of charge, and are licensed under the GNU General Public License, version 2, just like WordPress.'), 'http://wordpress.org/extend/themes/') . '</p>';
$help .= '<p>' . __('You can Search for themes by keyword, author, or tag, or can get more specific and search by criteria listed in the feature filter. Alternately, you can browse the themes that are Featured, Newest, or Recently Updated. When you find a theme you like, you can preview it or install it.') . '</p>';
$help .= '<p>' . __('You can Upload a theme manually if you have already downloaded its ZIP archive onto your computer (make sure it is from a trusted and original source). You can also do it the old-fashioned way and copy a downloaded theme&#8217;s folder via FTP into your <code>/wp-content/themes</code> directory.') . '</p>';
$help .= '<p><strong>' . __('For more information:') . '</strong></p>';
$help .= '<p>' . __('<a href="http://codex.wordpress.org/Using_Themes#Adding_New_Themes" target="_blank">Documentation on Adding New Themes</a>') . '</p>';
$help .= '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>';
add_contextual_help($current_screen, $help);

include('./admin-header.php');
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><a href="themes.php" class="nav-tab"><?php echo esc_html_x('Manage Themes', 'theme'); ?></a><a href="theme-install.php" class="nav-tab nav-tab-active"><?php echo esc_html( $title ); ?></a></h2>

	<ul class="subsubsub">
<?php
$display_tabs = array();
foreach ( (array) $tabs as $action => $text ) {
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

