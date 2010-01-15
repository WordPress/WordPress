<?php
/**
 * Install theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('install_themes') )
	wp_die(__('You do not have sufficient permissions to install themes on this blog.'));

include(ABSPATH . 'wp-admin/includes/theme-install.php');

$title = __('Install Themes');
$parent_file = 'themes.php';

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
if( empty($tab) || ( ! isset($tabs[ $tab ]) && ! in_array($tab, (array)$nonmenu_tabs) ) ) {
	$tab_actions = array_keys($tabs);
	$tab = $tab_actions[0];
}
if( empty($paged) )
	$paged = 1;

$body_id = $tab;

do_action('install_themes_pre_' . $tab); //Used to override the general interface, Eg, install or theme information.

add_contextual_help($current_screen, plugins_search_help());

include('admin-header.php');
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
	$href = admin_url('theme-install.php?tab='. $action);
	echo "\t\t<li><a href='$href'$class>$text</a>$sep</li>\n";
}
?>
	</ul>
	<br class="clear" />
	<?php do_action('install_themes_' . $tab, $paged); ?>
</div>
<?php
include('admin-footer.php');
