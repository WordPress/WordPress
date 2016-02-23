<?php
/**
 * Import WordPress Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

define('WP_LOAD_IMPORTERS', true);

/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( !current_user_can('import') )
	wp_die(__('You do not have sufficient permissions to import content in this site.'));

$title = __('Import');

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => '<p>' . __('This screen lists links to plugins to import data from blogging/content management platforms. Choose the platform you want to import from, and click Install Now when you are prompted in the popup window. If your platform is not listed, click the link to search the plugin directory for other importer plugins to see if there is one for your platform.') . '</p>' .
		'<p>' . __('In previous versions of WordPress, all importers were built-in. They have been turned into plugins since most people only use them once or infrequently.') . '</p>',
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Tools_Import_Screen" target="_blank">Documentation on Import</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if ( current_user_can( 'install_plugins' ) )
	$popular_importers = wp_get_popular_importers();
else
	$popular_importers = array();

// Detect and redirect invalid importers like 'movabletype', which is registered as 'mt'
if ( ! empty( $_GET['invalid'] ) && isset( $popular_importers[ $_GET['invalid'] ] ) ) {
	$importer_id = $popular_importers[ $_GET['invalid'] ]['importer-id'];
	if ( $importer_id != $_GET['invalid'] ) { // Prevent redirect loops.
		wp_redirect( admin_url( 'admin.php?import=' . $importer_id ) );
		exit;
	}
	unset( $importer_id );
}

add_thickbox();
wp_enqueue_script( 'plugin-install' );

require_once( ABSPATH . 'wp-admin/admin-header.php' );
$parent_file = 'tools.php';
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>
<?php if ( ! empty( $_GET['invalid'] ) ) : ?>
	<div class="error">
		<p><strong><?php _e( 'ERROR:' ); ?></strong> <?php
			/* translators: %s: importer slug */
			printf( __( 'The %s importer is invalid or is not installed.' ), '<strong>' . esc_html( $_GET['invalid'] ) . '</strong>' );
		?></p>
	</div>
<?php endif; ?>
<p><?php _e('If you have posts or comments in another system, WordPress can import those into this site. To get started, choose a system to import from below:'); ?></p>

<?php

$importers = get_importers();

// If a popular importer is not registered, create a dummy registration that links to the plugin installer.
foreach ( $popular_importers as $pop_importer => $pop_data ) {
	if ( isset( $importers[ $pop_importer ] ) )
		continue;
	if ( isset( $importers[ $pop_data['importer-id'] ] ) )
		continue;
	$importers[ $pop_data['importer-id'] ] = array( $pop_data['name'], $pop_data['description'], 'install' => $pop_data['plugin-slug'] );
}

if ( empty( $importers ) ) {
	echo '<p>' . __('No importers are available.') . '</p>'; // TODO: make more helpful
} else {
	uasort( $importers, '_usort_by_first_member' );
?>
<table class="widefat importers striped">

<?php
	foreach ($importers as $importer_id => $data) {
		$action = '';
		if ( isset( $data['install'] ) ) {
			$plugin_slug = $data['install'];
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
			if ( empty($action) ) {
				if ( is_main_site() ) {
					$action = '<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_slug .
										'&from=import&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="' .
										esc_attr__('Install importer') . '">' . $data[0] . '</a>';
				} else {
					$action = $data[0];
					$data[1] = sprintf( __( 'This importer is not installed. Please install importers from <a href="%s">the main site</a>.' ), get_admin_url( $current_site->blog_id, 'import.php' ) );
				}
			}
		} else {
			$action = "<a href='" . esc_url( "admin.php?import=$importer_id" ) . "' title='" . esc_attr( wptexturize( strip_tags( $data[1] ) ) ) ."'>{$data[0]}</a>";
		}

		echo "
			<tr>
				<td class='import-system row-title'>$action</td>
				<td class='desc'>{$data[1]}</td>
			</tr>";
	}
?>

</table>
<?php
}

if ( current_user_can('install_plugins') )
	echo '<p>' . sprintf( __('If the importer you need is not listed, <a href="%s">search the plugin directory</a> to see if an importer is available.'), esc_url( network_admin_url( 'plugin-install.php?tab=search&type=tag&s=importer' ) ) ) . '</p>';
?>

</div>

<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );
