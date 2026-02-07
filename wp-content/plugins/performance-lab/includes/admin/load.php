<?php
/**
 * Admin integration file.
 *
 * @package performance-lab
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Adds the features page to the Settings menu.
 *
 * @since 1.0.0
 * @since 3.0.0 Renamed to perflab_add_features_page().
 */
function perflab_add_features_page(): void {
	$hook_suffix = add_options_page(
		__( 'Performance Features', 'performance-lab' ),
		__( 'Performance', 'performance-lab' ),
		'manage_options',
		PERFLAB_SCREEN,
		'perflab_render_settings_page'
	);

	// Add the following hooks only if the screen was successfully added.
	if ( false !== $hook_suffix ) {
		add_action( "load-{$hook_suffix}", 'perflab_load_features_page', 10, 0 );
		add_filter( 'plugin_action_links_' . plugin_basename( PERFLAB_MAIN_FILE ), 'perflab_plugin_action_links_add_settings' );
	}
}

add_action( 'admin_menu', 'perflab_add_features_page' );

/**
 * Initializes functionality for the features page.
 *
 * @since 1.0.0
 * @since 3.0.0 Renamed to perflab_load_features_page(), and the
 *              $module and $hook_suffix parameters were removed.
 */
function perflab_load_features_page(): void {
	// Handle script enqueuing for settings page.
	add_action( 'admin_enqueue_scripts', 'perflab_enqueue_features_page_scripts' );

	// Handle admin notices for settings page.
	add_action( 'admin_notices', 'perflab_plugin_admin_notices' );

	// Handle style for settings page.
	add_action( 'admin_head', 'perflab_print_features_page_style' );
}

/**
 * Renders the plugin page.
 *
 * @since 1.0.0
 * @since 3.0.0 Renamed to perflab_render_settings_page().
 */
function perflab_render_settings_page(): void {
	?>
	<div class="wrap">
		<?php perflab_render_plugins_ui(); ?>
	</div>
	<?php
}

/**
 * Gets dismissed admin pointer IDs.
 *
 * @since 4.0.0
 *
 * @return non-empty-string[] Dismissed admin pointer IDs.
 */
function perflab_get_dismissed_admin_pointer_ids(): array {
	return array_filter(
		explode(
			',',
			(string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true )
		)
	);
}

/**
 * Gets the admin pointers.
 *
 * @since 4.0.0
 *
 * @return array<non-empty-string, array{ content: string, plugin: non-empty-string, dismiss_if_installed: bool }> Keys are the admin pointer IDs.
 */
function perflab_get_admin_pointers(): array {
	$pointers = array(
		'perflab-admin-pointer'            => array(
			'content'              => __( 'You can now test upcoming WordPress performance features.', 'performance-lab' ),
			'plugin'               => 'performance-lab',
			'dismiss_if_installed' => false,
		),
		'perflab-feature-view-transitions' => array(
			'content'              => __( 'New <strong>View Transitions</strong> feature now available.', 'performance-lab' ),
			'plugin'               => 'view-transitions',
			'dismiss_if_installed' => true,
		),
		'perflab-feature-nocache-bfcache'  => array(
			'content'              => __( 'New <strong>No-cache BFCache</strong> feature now available.', 'performance-lab' ),
			'plugin'               => 'nocache-bfcache',
			'dismiss_if_installed' => true,
		),
	);

	$installed_plugins = get_plugins();
	if (
		isset( $installed_plugins['speculation-rules/load.php']['Version'] )
		&&
		version_compare( $installed_plugins['speculation-rules/load.php']['Version'], '1.6.0', '>=' )
	) {
		$pointers['perflab-feature-speculation-rules-auth'] = array(
			'content'              => __( '<strong>Speculative Loading</strong> now includes an opt-in setting for logged-in users.', 'performance-lab' ),
			'plugin'               => 'speculative-loading',
			'dismiss_if_installed' => false,
		);
	}

	return $pointers;
}

/**
 * Initializes admin pointer.
 *
 * Handles the bootstrapping of the admin pointer.
 * Mainly jQuery code that is self-initialising.
 *
 * @since 1.0.0
 *
 * @param string|null $hook_suffix The current admin page. Note this can be null because `iframe_header()` does not
 *                                 ensure that `$hook_suffix` is a string when it calls `do_action( 'admin_enqueue_scripts', $hook_suffix )`.
 */
function perflab_admin_pointer( ?string $hook_suffix = '' ): void {
	// See get_plugin_page_hookname().
	$is_performance_screen = 'settings_page_' . PERFLAB_SCREEN === $hook_suffix;

	// Do not show admin pointer in multisite Network admin, User admin UI, dashboard, or plugins list table. However,
	// do proceed on the Performance screen so that all pointers can be auto-dismissed.
	if (
		is_network_admin() ||
		is_user_admin() ||
		(
			! in_array( $hook_suffix, array( 'index.php', 'plugins.php' ), true ) &&
			! $is_performance_screen
		)
	) {
		return;
	}

	$admin_pointers        = perflab_get_admin_pointers();
	$admin_pointer_ids     = array_keys( $admin_pointers );
	$dismissed_pointer_ids = perflab_get_dismissed_admin_pointer_ids();

	// And if we're on the Performance screen, automatically dismiss all the pointers.
	$auto_dismissed_pointer_ids = array();
	if ( $is_performance_screen ) {
		$auto_dismissed_pointer_ids = array_merge( $auto_dismissed_pointer_ids, $admin_pointer_ids );
	}

	// List of pointer IDs that are tied to feature plugin slugs.
	$plugin_pointers_dismissed_if_installed = array();
	foreach ( $admin_pointers as $pointer_id => $admin_pointer ) {
		if ( $admin_pointer['dismiss_if_installed'] ) {
			$plugin_pointers_dismissed_if_installed[ $pointer_id ] = $admin_pointer['plugin'];
		}
	}

	// Preemptively dismiss plugin-specific pointers for plugins which are already installed.
	$plugin_dependent_pointers_undismissed = array_diff( array_keys( $plugin_pointers_dismissed_if_installed ), $dismissed_pointer_ids );
	if ( count( $plugin_dependent_pointers_undismissed ) > 0 ) {
		/**
		 * Installed plugin slugs.
		 *
		 * @var non-empty-string[] $installed_plugin_slugs
		 */
		$installed_plugin_slugs = array_map(
			static function ( $name ) {
				return strtok( $name, '/' );
			},
			array_keys( get_plugins() )
		);

		foreach ( $plugin_dependent_pointers_undismissed as $pointer_id ) {
			if (
				in_array( $plugin_pointers_dismissed_if_installed[ $pointer_id ], $installed_plugin_slugs, true ) &&
				! in_array( $pointer_id, $dismissed_pointer_ids, true )
			) {
				$auto_dismissed_pointer_ids[] = $pointer_id;
			}
		}
	}

	// Persist the automatically-dismissed pointers.
	if ( count( $auto_dismissed_pointer_ids ) > 0 ) {
		$dismissed_pointer_ids = array_unique( array_merge( $dismissed_pointer_ids, $auto_dismissed_pointer_ids ) );
		update_user_meta(
			get_current_user_id(),
			'dismissed_wp_pointers',
			implode( ',', $dismissed_pointer_ids )
		);
	}

	// Determine which admin pointers we need.
	$new_install_pointer_id = 'perflab-admin-pointer';
	if ( ! in_array( $new_install_pointer_id, $dismissed_pointer_ids, true ) ) {
		$needed_pointer_ids = array( $new_install_pointer_id );
	} else {
		$needed_pointer_ids = $admin_pointer_ids;
	}
	$needed_pointer_ids = array_diff( $needed_pointer_ids, $dismissed_pointer_ids );

	// No admin pointers are needed, so abort.
	if ( count( $needed_pointer_ids ) === 0 ) {
		return;
	}

	// Enqueue pointer CSS and JS.
	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );

	$args = array(
		'heading' => __( 'Performance Lab', 'performance-lab' ),
	);

	$args['content'] = implode(
		'',
		array_map(
			static function ( string $needed_pointer ) use ( $admin_pointers ): string {
				return '<p>' . $admin_pointers[ $needed_pointer ]['content'] . '</p>';
			},
			$needed_pointer_ids
		)
	);

	$args['content'] .= '<p>' . sprintf(
		/* translators: %s: settings page link */
		esc_html__( 'Open %s to individually toggle the performance features and access any relevant settings.', 'performance-lab' ),
		'<a href="' . esc_url( add_query_arg( 'page', PERFLAB_SCREEN, admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'Settings > Performance', 'performance-lab' ) . '</a>'
	) . '</p>';

	$wp_kses_options = array(
		'a'      => array(
			'href' => array(),
		),
		'p'      => array(),
		'strong' => array(),
	);

	$pointer_ids_to_dismiss = array_values( array_diff( $admin_pointer_ids, $dismissed_pointer_ids ) );

	ob_start();
	?>
	<script>
		jQuery( function() {
			const pointerIdsToDismiss = <?php echo wp_json_encode( $pointer_ids_to_dismiss, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_OBJECT_AS_ARRAY ); ?>;
			const nonce = <?php echo wp_json_encode( wp_create_nonce( 'dismiss_pointer' ), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ); ?>;

			function dismissNextPointer() {
				const pointerId = pointerIdsToDismiss.shift();
				if ( ! pointerId ) {
					return;
				}

				jQuery.post(
					window.ajaxurl,
					{
						pointer: pointerId,
						action:  'dismiss-wp-pointer',
						_wpnonce: nonce,
					}
				).then( dismissNextPointer );
			}

			// Pointer Options.
			const options = {
				content: <?php echo wp_json_encode( '<h3>' . esc_html( $args['heading'] ) . '</h3>' . wp_kses( $args['content'], $wp_kses_options ), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ); ?>,
				position: {
					edge:  'left',
					align: 'right',
				},
				pointerClass: 'wp-pointer arrow-top',
				pointerWidth: 420,
				close: dismissNextPointer
			};

			jQuery( '#menu-settings' ).pointer( options ).pointer( 'open' );
		} );
	</script>
	<?php
	$processor = new WP_HTML_Tag_Processor( (string) ob_get_clean() );
	if ( $processor->next_tag( array( 'tag_name' => 'SCRIPT' ) ) ) {
		wp_add_inline_script( 'wp-pointer', $processor->get_modifiable_text() );
	}
}
add_action( 'admin_enqueue_scripts', 'perflab_admin_pointer' );

/**
 * Adds a link to the features page to the plugin's entry in the plugins list table.
 *
 * This function is only used if the features page exists and is accessible.
 *
 * @since 1.0.0
 *
 * @see perflab_add_features_page()
 *
 * @param string[]|mixed $links List of plugin action links HTML.
 * @return string[]|mixed Modified list of plugin action links HTML.
 */
function perflab_plugin_action_links_add_settings( $links ) {
	if ( ! is_array( $links ) ) {
		return $links;
	}

	// Add link as the first plugin action link.
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( add_query_arg( 'page', PERFLAB_SCREEN, admin_url( 'options-general.php' ) ) ),
		esc_html__( 'Settings', 'performance-lab' )
	);

	return array_merge(
		array( 'settings' => $settings_link ),
		$links
	);
}

/**
 * Dismisses notification pointer after verifying nonce.
 *
 * This function adds a nonce check before dismissing perflab-admin-pointer
 * It runs before the dismiss-wp-pointer AJAX action is performed.
 *
 * @since 2.3.0
 */
function perflab_dismiss_wp_pointer_wrapper(): void {
	if (
		isset( $_POST['pointer'] )
		&&
		! in_array( $_POST['pointer'], array_keys( perflab_get_admin_pointers() ), true )
	) {
		// Another plugin's pointer, do nothing.
		return;
	}
	check_ajax_referer( 'dismiss_pointer' );
}
add_action( 'wp_ajax_dismiss-wp-pointer', 'perflab_dismiss_wp_pointer_wrapper', 0 );

/**
 * Gets the path to a script or stylesheet.
 *
 * @since 3.7.0
 *
 * @param string      $src_path Source path.
 * @param string|null $min_path Minified path. If not supplied, then '.min' is injected before the file extension in the source path.
 * @return string URL to script or stylesheet.
 */
function perflab_get_asset_path( string $src_path, ?string $min_path = null ): string {
	if ( null === $min_path ) {
		// Note: wp_scripts_get_suffix() is not used here because we need access to both the source and minified paths.
		$min_path = (string) preg_replace( '/(?=\.\w+$)/', '.min', $src_path );
	}

	$force_src = false;
	if ( WP_DEBUG && ! file_exists( trailingslashit( PERFLAB_PLUGIN_DIR_PATH ) . $min_path ) ) {
		$force_src = true;
		wp_trigger_error(
			__FUNCTION__,
			sprintf(
				/* translators: %s is the minified asset path */
				__( 'Minified asset has not been built: %s', 'performance-lab' ),
				$min_path
			),
			E_USER_WARNING
		);
	}

	if ( SCRIPT_DEBUG || $force_src ) {
		return $src_path;
	}

	return $min_path;
}

/**
 * Callback function to handle admin scripts.
 *
 * @since 2.8.0
 * @since 3.0.0 Renamed to perflab_enqueue_features_page_scripts().
 */
function perflab_enqueue_features_page_scripts(): void {
	// These assets are needed for the "Learn more" popover.
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script( 'plugin-install' );

	// Enqueue plugin activate AJAX script and localize script data.
	wp_enqueue_script(
		'perflab-plugin-activate-ajax',
		plugins_url( perflab_get_asset_path( 'includes/admin/plugin-activate-ajax.js' ), PERFLAB_MAIN_FILE ),
		array( 'wp-i18n', 'wp-a11y', 'wp-api-fetch' ),
		PERFLAB_VERSION,
		true
	);
}

/**
 * Sanitizes a plugin slug.
 *
 * @since 3.1.0
 *
 * @param mixed $unsanitized_plugin_slug Unsanitized plugin slug.
 * @return string|null Validated and sanitized slug or else null.
 */
function perflab_sanitize_plugin_slug( $unsanitized_plugin_slug ): ?string {
	if ( in_array( $unsanitized_plugin_slug, perflab_get_standalone_plugins(), true ) ) {
		return $unsanitized_plugin_slug;
	}
	return null;
}

/**
 * Callback for handling installation/activation of plugin.
 *
 * @since 3.0.0
 */
function perflab_install_activate_plugin_callback(): void {
	check_admin_referer( 'perflab_install_activate_plugin' );

	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

	if ( ! isset( $_GET['slug'] ) ) {
		wp_die( esc_html__( 'Missing required parameter.', 'performance-lab' ) );
	}

	$plugin_slug = perflab_sanitize_plugin_slug( wp_unslash( $_GET['slug'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- perflab_sanitize_plugin_slug() is a sanitizing function.
	if ( null === $plugin_slug ) {
		wp_die( esc_html__( 'Invalid plugin.', 'performance-lab' ) );
	}

	// Install and activate the plugin and its dependencies.
	$result = perflab_install_and_activate_plugin( $plugin_slug );
	if ( $result instanceof WP_Error ) {
		wp_die( wp_kses_post( $result->get_error_message() ) );
	}

	$url = add_query_arg(
		array(
			'page'     => PERFLAB_SCREEN,
			'activate' => $plugin_slug,
		),
		admin_url( 'options-general.php' )
	);

	if ( wp_safe_redirect( $url ) ) {
		exit;
	}
}
add_action( 'admin_action_perflab_install_activate_plugin', 'perflab_install_activate_plugin_callback' );

/**
 * Callback function to handle admin inline style.
 *
 * @since 3.0.0
 */
function perflab_print_features_page_style(): void {
	?>
<style>
	.plugin-card .name,
	.plugin-card .desc, /* For WP <6.5 versions */
	.plugin-card .desc > p {
		margin-left: 0;
	}
	.plugin-card-top {
		/* This is required to ensure the Settings link does not extend below the bottom of a plugin card on a wide screen. */
		min-height: 90px;
	}
	@media screen and (max-width: 782px) {
		.plugin-card-top {
			/* Same reason as above, but now the button is taller to make it easier to tap on touch screens. */
			min-height: 110px;
		}
	}
	.plugin-card .perflab-plugin-experimental {
		font-size: 80%;
		font-weight: normal;
	}

	@media screen and (max-width: 1100px) and (min-width: 782px), (max-width: 480px) {
		.plugin-card .action-links {
			margin-left: auto;
		}
		/* Make sure the settings link gets spaced out from the Learn more link. */
		.plugin-card .plugin-action-buttons > li:nth-child(3) {
			margin-left: 2ex;
			border-left: solid 1px;
			padding-left: 2ex;
		}
	}
</style>
	<?php
}

/**
 * Callback function hooked to admin_notices to render admin notices on the plugin's screen.
 *
 * @since 2.8.0
 */
function perflab_plugin_admin_notices(): void {
	if ( ! current_user_can( 'install_plugins' ) ) {
		$are_all_plugins_installed = true;
		$installed_plugin_slugs    = array_map(
			static function ( $name ) {
				return strtok( $name, '/' );
			},
			array_keys( get_plugins() )
		);
		foreach ( perflab_get_standalone_plugin_version_constants() as $plugin_slug => $constant_name ) {
			if ( ! in_array( $plugin_slug, $installed_plugin_slugs, true ) ) {
				$are_all_plugins_installed = false;
				break;
			}
		}

		if ( ! $are_all_plugins_installed ) {
			wp_admin_notice(
				esc_html__( 'Due to your site\'s configuration, you may not be able to activate the performance features, unless the underlying plugin is already installed. Please install the relevant plugins manually.', 'performance-lab' ),
				array(
					'type' => 'warning',
				)
			);
			return;
		}
	}

	$activated_plugin_slug = null;
	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$activated_plugin_slug = perflab_sanitize_plugin_slug( wp_unslash( $_GET['activate'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- perflab_sanitize_plugin_slug() is a sanitizing function.
	}

	if ( null !== $activated_plugin_slug ) {
		$message = __( 'Feature activated.', 'performance-lab' );

		$plugin_settings_url = perflab_get_plugin_settings_url( $activated_plugin_slug );
		if ( null !== $plugin_settings_url ) {
			/* translators: %s is the settings URL */
			$message .= ' ' . sprintf( __( 'Review <a href="%s">settings</a>.', 'performance-lab' ), esc_url( $plugin_settings_url ) );
		}

		wp_admin_notice(
			wp_kses(
				$message,
				array(
					'a' => array(
						'href' => array(),
					),
				)
			),
			array(
				'type'        => 'success',
				'dismissible' => true,
			)
		);
	}
}

/**
 * Gets the URL to the plugin settings screen if one exists.
 *
 * @since 3.1.0
 *
 * @param string $plugin_slug Plugin slug passed to generate the settings link.
 * @return string|null Either the plugin settings URL or null if not available.
 */
function perflab_get_plugin_settings_url( string $plugin_slug ): ?string {
	$plugin_file = null;

	foreach ( array_keys( get_plugins() ) as $file ) {
		if ( strtok( $file, '/' ) === $plugin_slug ) {
			$plugin_file = $file;
			break;
		}
	}

	if ( null === $plugin_file ) {
		return null;
	}

	/** This filter is documented in wp-admin/includes/class-wp-plugins-list-table.php */
	$plugin_links = apply_filters( "plugin_action_links_{$plugin_file}", array() );

	if ( ! is_array( $plugin_links ) || ! array_key_exists( 'settings', $plugin_links ) ) {
		return null;
	}

	$p = new WP_HTML_Tag_Processor( $plugin_links['settings'] );
	if ( ! $p->next_tag( array( 'tag_name' => 'A' ) ) ) {
		return null;
	}
	$href = $p->get_attribute( 'href' );
	if ( is_string( $href ) && '' !== $href ) {
		return $href;
	}

	return null;
}

/**
 * Prints the Performance Lab install notice after each feature plugin's row meta.
 *
 * @since 3.2.0
 *
 * @param string $plugin_file Plugin file.
 */
function perflab_print_row_meta_install_notice( string $plugin_file ): void {
	if ( ! in_array( strtok( $plugin_file, '/' ), perflab_get_standalone_plugins(), true ) ) {
		return;
	}

	$message = sprintf(
		/* translators: %s: link to Performance Lab settings screen */
		__( 'This plugin is installed by <a href="%s">Performance Lab</a>.', 'performance-lab' ),
		esc_url( add_query_arg( 'page', PERFLAB_SCREEN, admin_url( 'options-general.php' ) ) )
	);

	printf(
		'<div class="requires"><p>%1$s</p></div>',
		wp_kses( $message, array( 'a' => array( 'href' => array() ) ) )
	);
}
add_action( 'after_plugin_row_meta', 'perflab_print_row_meta_install_notice' );
