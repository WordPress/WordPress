<?php
require_once('admin.php');

if ( ! current_user_can('install_plugins') )
	wp_die(__('You do not have sufficient permissions to install plugins on this blog.'));

include(ABSPATH . 'wp-admin/includes/plugin-install.php');

$title = __('Install Plugins');
$parent_file = 'plugins.php';

wp_reset_vars( array('tab', 'paged') );
wp_enqueue_style( 'plugin-install' );
wp_enqueue_script( 'plugin-install' );
add_thickbox();

//These are the tabs which are shown on the page, Note that 'install' and 'plugin-information' are valid hooks, but not shown here due to not requiring the header
$tabs = array(
			'search'	=> __('Search Plugins'),
			'upload'	=> __('Upload a Plugin'),
			'featured'	=> __('Featured Plugins'),
			'popular'	=> __('Popular Plugins'),
			'new'		=> __('Newest Plugins'),
			'updated'	=> __('Recently Updated Plugins')
		);

$tabs = apply_filters('install_plugins_tabs', $tabs );

if( empty($tab) || ( ! isset($tabs[ $tab ]) && ! in_array($tab, array('install', 'plugin-information')) ) ){
	$tab_actions = array_keys($tabs);
	$tab = $tab_actions[0];
}
if( empty($paged) )
	$paged = 1;

$body_id = $tab;

do_action('install_plugins_pre_' . $tab);

include('admin-header.php');
?>
<div class="wrap">
	<h2><?php _e('Install Plugins') ?></h2>
	<ul class="subsubsub">
<?php
$display_tabs = array();
foreach ( (array)$tabs as $action => $text ) {
	$sep = ( end($tabs) != $text ) ? ' | ' : '';
	$class = ( $action == $tab ) ? ' class="current"' : '';
	$href = admin_url('plugin-install.php?tab='. $action);
	echo "\t\t<li><a href='$href'$class>$text</a>$sep</li>\n";
}
?>
	</ul>
	<?php do_action('install_plugins_' . $tab, $paged); ?>
</div>
<?php
include('admin-footer.php');
?>