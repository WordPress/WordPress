<?php
/**
 * Connectors administration screen.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 7.0.0
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to manage connectors on this site.' ) . '</p>',
		403
	);
}

if ( ! class_exists( '\WordPress\AiClient\AiClient' ) || ! function_exists( 'wp_options_connectors_wp_admin_render_page' ) ) {
	wp_die(
		'<h1>' . __( 'The Connectors are not available.' ) . '</h1>' .
		'<p>' . __( 'The Connectors page requires build files. Please build WordPress and try again.' ) . '</p>',
		503
	);
}

// Set the page title.
$title = __( 'Connectors' );

// Set parent file for menu highlighting.
$parent_file = 'options-general.php';

/**
 * Preloads the REST API responses the Connectors UI fetches on mount.
 *
 * Without this, the page does a network round-trip for site settings,
 * plugin capability discovery, and each connector's plugin record after
 * the JS hydrates, which noticeably delays first paint.
 *
 * @since 7.2.0
 * @access private
 *
 * @param array<string|string[]> $preload_paths Paths already queued for preloading.
 * @return array<string|string[]> Paths with the Connectors-specific requests appended.
 *
 * @phpstan-param array<non-falsy-string|array{ non-falsy-string, 'GET'|'OPTIONS' }> $preload_paths
 * @phpstan-return array<non-falsy-string|array{ non-falsy-string, 'GET'|'OPTIONS' }>
 */
function _wp_connectors_preload_paths( array $preload_paths ): array {
	// getEntityRecord( 'root', 'site' ) in use-connector-plugin.ts / ai-plugin-callout.tsx.
	$preload_paths[] = '/wp/v2/settings';

	// canUser( 'create', { kind: 'root', name: 'plugin' } ) in stage.tsx.
	$preload_paths[] = array( '/wp/v2/plugins', 'OPTIONS' );

	// AiPluginCallout in routes/connectors-home/ai-plugin-callout.tsx queries this
	// hardcoded ID to check whether the WP AI plugin is installed/active.
	$preload_paths[] = '/wp/v2/plugins/ai/ai?context=edit';

	// getEntityRecord( 'root', 'plugin', <basename> ) per connector in use-connector-plugin.ts.
	foreach ( wp_get_connectors() as $connector_data ) {
		if ( empty( $connector_data['plugin']['file'] ) ) {
			continue;
		}
		// core-data's plugin entity uses the basename with `.php` stripped
		// as the record key (see routes/connectors-home/use-connector-plugin.ts).
		$basename        = preg_replace( '/\.php$/', '', plugin_basename( $connector_data['plugin']['file'] ) );
		$preload_paths[] = '/wp/v2/plugins/' . $basename . '?context=edit';
	}

	return $preload_paths;
}
add_filter( 'wp_options_connectors_wp_admin_preload_paths', '_wp_connectors_preload_paths' );

require_once ABSPATH . 'wp-admin/admin-header.php';

// Render the Connectors page.
wp_options_connectors_wp_admin_render_page();

require_once ABSPATH . 'wp-admin/admin-footer.php';
