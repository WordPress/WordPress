<?php
/**
 * Import WordPress Administration Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

define('WP_LOAD_IMPORTERS', true);

/** Load WordPress Bootstrap */
require_once ('admin.php');

if ( !current_user_can('import') )
	wp_die(__('You do not have sufficient permissions to import content in this site.'));

$title = __('Import');

add_contextual_help($current_screen,
	'<p>' . __('This screen lists links to plugins to import data from blogging/content management platforms. Choose the platform you want to import from, and click Install Now when you are prompted in the popup window. If your platform is not listed, click the link to search the plugin directory for other importer plugins to see if there is one for your platform.') . '</p>' .
	'<p>' . __('In previous versions of WordPress, all the importers were built-in, but they have been turned into plugins as of version 3.0 since most people only use them once or infrequently.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Tools_Import_SubPanel" target="_blank">Documentation on Import</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

$popular_importers = array();
if ( current_user_can('install_plugins') )
	$popular_importers = array(
		'blogger' => array( __('Blogger'), __('Install the Blogger importer to import posts, comments, and users from a Blogger blog.'), 'install' ),
		'wpcat2tag' => array(__('Categories and Tags Converter'), __('Install the category/tag converter to convert existing categories to tags or tags to categories, selectively.'), 'install', 'wp-cat2tag' ),
		'livejournal' => array( __( 'LiveJournal' ), __( 'Install the LiveJournal importer to import posts from LiveJournal using their API.' ), 'install' ),
		'movabletype' => array( __('Movable Type and TypePad'), __('Install the Movable Type importer to import posts and comments from a Movable Type or TypePad blog.'), 'install', 'mt' ),
		'opml' => array( __('Blogroll'), __('Install the blogroll importer to import links in OPML format.'), 'install' ),
		'rss' => array( __('RSS'), __('Install the RSS importer to import posts from an RSS feed.'), 'install' ),
		'wordpress' => array( 'WordPress', __('Install the WordPress importer to import posts, pages, comments, custom fields, categories, and tags from a WordPress export file.'), 'install' )
	);

if ( ! empty( $_GET['invalid'] ) && !empty($popular_importers[$_GET['invalid']][3]) ) {
	wp_redirect( admin_url('import.php?import=' . $popular_importers[$_GET['invalid']][3]) );
	exit;
}

add_thickbox();
wp_enqueue_script( 'plugin-install' );
wp_admin_css( 'plugin-install' );

require_once ('admin-header.php');
$parent_file = 'tools.php';
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<?php if ( ! empty( $_GET['invalid'] ) ) : ?>
	<div class="error"><p><strong><?php _e('ERROR:')?></strong> <?php printf( __('The <strong>%s</strong> importer is invalid or is not installed.'), esc_html( $_GET['invalid'] ) ); ?></p></div>
<?php endif; ?>
<p><?php _e('If you have posts or comments in another system, WordPress can import those into this site. To get started, choose a system to import from below:'); ?></p>

<?php

// Load all importers so that they can register.
$import_loc = 'wp-admin/import';
$import_root = ABSPATH . $import_loc;

if ( file_exists( $import_root ) ) {
	$imports_dir = opendir($import_root);
	if ($imports_dir) {
		while (($file = readdir($imports_dir)) !== false) {
			if ($file[0] == '.') {
				continue;
			} elseif (substr($file, -4) == '.php') {
				require_once($import_root . '/' . $file);
			}
		}
	}
	closedir( $imports_dir );
}

$importers = get_importers();

// If a popular importer is not registered, create a dummy registration that links to the plugin installer.
foreach ( $popular_importers as $pop_importer => $pop_data ) {
	if ( isset($importers[$pop_importer] ) )
		continue;
	if ( isset( $pop_data[3] ) && isset( $importers[ $pop_data[3] ] ) )
		continue;

	$importers[$pop_importer] = $popular_importers[$pop_importer];
}

if (empty ($importers)) {
	echo '<p>'.__('No importers are available.').'</p>'; // TODO: make more helpful
} else {
	uasort($importers, create_function('$a, $b', 'return strcmp($a[0], $b[0]);'));
?>
<table class="widefat" cellspacing="0">

<?php
	$style = '';
	foreach ($importers as $id => $data) {
		$style = ('class="alternate"' == $style || 'class="alternate active"' == $style) ? '' : 'alternate';
		$action = '';
		if ( 'install' == $data[2] ) {
			$plugin_slug = $id . '-importer';
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
				// Looks like Importer is installed, But not active
				$plugins = get_plugins( '/' . $plugin_slug );
				if ( !empty($plugins) ) {
					$keys = array_keys($plugins);
					$plugin_file = $plugin_slug . '/' . $keys[0];
					$action = '<a href="' . esc_url(wp_nonce_url(admin_url('plugins.php?action=activate&plugin=' . $plugin_file . '&from=import'), 'activate-plugin_' . $plugin_file)) .
											'"title="' . esc_attr__('Activate importer') . '"">' . $data[0] . '</a>';
				}
			}
			if ( empty($action) )
				$action = '<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_slug .
										'&from=import&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="' .
										esc_attr__('Install importer') . '">' . $data[0] . '</a>';
		} else {
			$action = "<a href='" . esc_url("admin.php?import=$id") . "' title='" . esc_attr( wptexturize(strip_tags($data[1])) ) ."'>{$data[0]}</a>";
		}

		if ($style != '')
			$style = 'class="'.$style.'"';
		echo "
			<tr $style>
				<td class='import-system row-title'>$action</td>
				<td class='desc'>{$data[1]}</td>
			</tr>";
	}
?>

</table>
<?php
}

if ( current_user_can('install_plugins') )
	echo '<p>' . sprintf( __('If the importer you need is not listed, <a href="%s">search the plugins directory</a> to see if an importer is available.'), esc_url( network_admin_url( 'plugin-install.php?tab=search&type=tag&s=importer' ) ) ) . '</p>';
?>

</div>

<?php

include ('admin-footer.php');
?>
