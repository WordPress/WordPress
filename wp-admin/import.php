<?php
/**
 * Import WordPress Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

define( 'WP_LOAD_IMPORTERS', true );

/** Load WordPress Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'import' ) ) {
	wp_die( __( 'Sorry, you are not allowed to import content into this site.' ) );
}

$title = __( 'Import' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => '<p>' . __( 'This screen lists links to plugins to import data from blogging/content management platforms. Choose the platform you want to import from, and click Install Now when you are prompted in the popup window. If your platform is not listed, click the link to search the plugin directory for other importer plugins to see if there is one for your platform.' ) . '</p>' .
			'<p>' . __( 'In previous versions of WordPress, all importers were built-in. They have been turned into plugins since most people only use them once or infrequently.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/tools-import-screen/">Documentation on Import</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

if ( current_user_can( 'install_plugins' ) ) {
	// List of popular importer plugins from the WordPress.org API.
	$popular_importers = wp_get_popular_importers();
} else {
	$popular_importers = array();
}

// Detect and redirect invalid importers like 'movabletype', which is registered as 'mt'.
if ( ! empty( $_GET['invalid'] ) && isset( $popular_importers[ $_GET['invalid'] ] ) ) {
	$importer_id = $popular_importers[ $_GET['invalid'] ]['importer-id'];
	if ( $importer_id !== $_GET['invalid'] ) { // Prevent redirect loops.
		wp_redirect( admin_url( 'admin.php?import=' . $importer_id ) );
		exit;
	}
	unset( $importer_id );
}

add_thickbox();
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'updates' );

require_once ABSPATH . 'wp-admin/admin-header.php';
$parent_file = 'tools.php';
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>
<?php if ( ! empty( $_GET['invalid'] ) ) : ?>
	<div class="error">
		<p><strong><?php _e( 'Error:' ); ?></strong>
			<?php
			/* translators: %s: Importer slug. */
			printf( __( 'The %s importer is invalid or is not installed.' ), '<strong>' . esc_html( $_GET['invalid'] ) . '</strong>' );
			?>
		</p>
	</div>
<?php endif; ?>
<p><?php _e( 'If you have posts or comments in another system, WordPress can import those into this site. To get started, choose a system to import from below:' ); ?></p>

<?php
// Registered (already installed) importers. They're stored in the global $wp_importers.
$importers = get_importers();

// If a popular importer is not registered, create a dummy registration that links to the plugin installer.
foreach ( $popular_importers as $pop_importer => $pop_data ) {
	if ( isset( $importers[ $pop_importer ] ) ) {
		continue;
	}
	if ( isset( $importers[ $pop_data['importer-id'] ] ) ) {
		continue;
	}

	// Fill the array of registered (already installed) importers with data of the popular importers from the WordPress.org API.
	$importers[ $pop_data['importer-id'] ] = array(
		$pop_data['name'],
		$pop_data['description'],
		'install' => $pop_data['plugin-slug'],
	);
}

if ( empty( $importers ) ) {
	echo '<p>' . __( 'No importers are available.' ) . '</p>'; // TODO: Make more helpful.
} else {
	uasort( $importers, '_usort_by_first_member' );
	?>
<table class="widefat importers striped">

	<?php
	foreach ( $importers as $importer_id => $data ) {
		$plugin_slug         = '';
		$action              = '';
		$is_plugin_installed = false;

		if ( isset( $data['install'] ) ) {
			$plugin_slug = $data['install'];

			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
				// Looks like an importer is installed, but not active.
				$plugins = get_plugins( '/' . $plugin_slug );
				if ( ! empty( $plugins ) ) {
					$keys        = array_keys( $plugins );
					$plugin_file = $plugin_slug . '/' . $keys[0];
					$url         = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'activate',
								'plugin' => $plugin_file,
								'from'   => 'import',
							),
							admin_url( 'plugins.php' )
						),
						'activate-plugin_' . $plugin_file
					);
					$action      = sprintf(
						'<a href="%s" aria-label="%s">%s</a>',
						esc_url( $url ),
						/* translators: %s: Importer name. */
						esc_attr( sprintf( __( 'Run %s' ), $data[0] ) ),
						__( 'Run Importer' )
					);

					$is_plugin_installed = true;
				}
			}

			if ( empty( $action ) ) {
				if ( is_main_site() ) {
					$url    = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'install-plugin',
								'plugin' => $plugin_slug,
								'from'   => 'import',
							),
							self_admin_url( 'update.php' )
						),
						'install-plugin_' . $plugin_slug
					);
					$action = sprintf(
						'<a href="%1$s" class="install-now" data-slug="%2$s" data-name="%3$s" aria-label="%4$s">%5$s</a>',
						esc_url( $url ),
						esc_attr( $plugin_slug ),
						esc_attr( $data[0] ),
						/* translators: %s: Importer name. */
						esc_attr( sprintf( _x( 'Install %s now', 'plugin' ), $data[0] ) ),
						__( 'Install Now' )
					);
				} else {
					$action = sprintf(
						/* translators: %s: URL to Import screen on the main site. */
						__( 'This importer is not installed. Please install importers from <a href="%s">the main site</a>.' ),
						get_admin_url( get_current_network_id(), 'import.php' )
					);
				}
			}
		} else {
			$url    = add_query_arg(
				array(
					'import' => $importer_id,
				),
				self_admin_url( 'admin.php' )
			);
			$action = sprintf(
				'<a href="%1$s" aria-label="%2$s">%3$s</a>',
				esc_url( $url ),
				/* translators: %s: Importer name. */
				esc_attr( sprintf( __( 'Run %s' ), $data[0] ) ),
				__( 'Run Importer' )
			);

			$is_plugin_installed = true;
		}

		if ( ! $is_plugin_installed && is_main_site() ) {
			$url     = add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => $plugin_slug,
					'from'      => 'import',
					'TB_iframe' => 'true',
					'width'     => 600,
					'height'    => 550,
				),
				network_admin_url( 'plugin-install.php' )
			);
			$action .= sprintf(
				' | <a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">%3$s</a>',
				esc_url( $url ),
				/* translators: %s: Importer name. */
				esc_attr( sprintf( __( 'More information about %s' ), $data[0] ) ),
				__( 'Details' )
			);
		}

		echo "
			<tr class='importer-item'>
				<td class='import-system'>
					<span class='importer-title'>{$data[0]}</span>
					<span class='importer-action'>{$action}</span>
				</td>
				<td class='desc'>
					<span class='importer-desc'>{$data[1]}</span>
				</td>
			</tr>";
	}
	?>
</table>
	<?php
}

if ( current_user_can( 'install_plugins' ) ) {
	echo '<p>' . sprintf(
		/* translators: %s: URL to Add Plugins screen. */
		__( 'If the importer you need is not listed, <a href="%s">search the plugin directory</a> to see if an importer is available.' ),
		esc_url( network_admin_url( 'plugin-install.php?tab=search&type=tag&s=importer' ) )
	) . '</p>';
}
?>

</div>

<?php
wp_print_request_filesystem_credentials_modal();
wp_print_admin_notice_templates();

require_once ABSPATH . 'wp-admin/admin-footer.php';
