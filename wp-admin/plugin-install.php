<?php
/**
 * Install plugin administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can('install_plugins') )
	wp_die(__('You do not have sufficient permissions to install plugins on this site.'));

include(ABSPATH . 'wp-admin/includes/plugin-install.php');

$title = __('Install Plugins');
$parent_file = 'plugins.php';

wp_reset_vars( array('tab', 'paged') );

//These are the tabs which are shown on the page,
$tabs = array();
$tabs['dashboard'] = __('Search');
if ( 'search' == $tab )
	$tabs['search']	= __('Search Results');
$tabs['upload'] = __('Upload');
$tabs['featured'] = _x('Featured','Plugin Installer');
$tabs['popular']  = _x('Popular','Plugin Installer');
$tabs['new']      = _x('Newest','Plugin Installer');
$tabs['updated']  = _x('Recently Updated','Plugin Installer');

$nonmenu_tabs = array('plugin-information'); //Valid actions to perform which do not have a Menu item.

$tabs = apply_filters('install_plugins_tabs', $tabs );
$nonmenu_tabs = apply_filters('install_plugins_nonmenu_tabs', $nonmenu_tabs);

//If a non-valid menu tab has been selected, And its not a non-menu action.
if ( empty($tab) || ( ! isset($tabs[ $tab ]) && ! in_array($tab, (array)$nonmenu_tabs) ) ) {
	$tab_actions = array_keys($tabs);
	$tab = $tab_actions[0];
}
if ( empty($paged) )
	$paged = 1;

wp_enqueue_style( 'plugin-install' );
wp_enqueue_script( 'plugin-install' );
if ( 'plugin-information' != $tab )
	add_thickbox();

$body_id = $tab;

do_action('install_plugins_pre_' . $tab); //Used to override the general interface, Eg, install or plugin information.

add_contextual_help($current_screen,
	'<p>' . __('Plugins hook into WordPress to extend its functionality with custom features. Plugins are developed independently from WordPress core by thousands of developers all over the world. All plugins in the official <a href="http://wordpress.org/extend/plugins/">WordPress.org Plugin Directory</a> are compatible with the WordPress GPL v2 license. You can find new plugins to install by searching or browsing the Directory right here in your own Plugins section.') . '</p>' .
	'<p>' . __('If you know what you&#8217;re looking for, Search is your best bet. The Search screen has options to Search in the WordPress.org Plugin Directory for a particular Term, Author, or Tag. You can also search the directory by selecting a popular tags. Tags in larger type mean more plugins have been labeled with that tag.') . '</p>' .
	'<p>' . __('If you just want to get an idea of what&#8217;s available, you can browse Featured, Popular, Newest, and Recently Updated plugins by using the links in the upper left of the screen. These sections rotate regularly.') . '</p>' .
	'<p>' . __('If you want to install a plugin that you&#8217;ve downloaded elsewhere, click Upload in the upper left. You will be prompted to upload the .zip package, and once uploaded, you can activate the new plugin.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Plugins_Add_New_SubPanel">Documentation on Installing Plugins</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/">Support Forums</a>') . '</p>'
);

include('./admin-header.php');
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

	<ul class="subsubsub">
<?php
$display_tabs = array();
foreach ( (array)$tabs as $action => $text ) {
	$sep = ( end($tabs) != $text ) ? ' | ' : '';
	$class = ( $action == $tab ) ? ' class="current"' : '';
	$href = admin_url('plugin-install.php?tab=' . $action);
	echo "\t\t<li><a href='$href'$class>$text</a>$sep</li>\n";
}
?>
	</ul>
	<br class="clear" />
	<?php do_action('install_plugins_' . $tab, $paged); ?>
</div>
<?php
include('./admin-footer.php');
