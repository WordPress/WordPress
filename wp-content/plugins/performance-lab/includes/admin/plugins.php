<?php
/**
 * Admin settings helper functions.
 *
 * @package performance-lab
 * @noinspection PhpRedundantOptionalArgumentInspection
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Gets plugin info for the given plugin slug from WordPress.org.
 *
 * @since 2.8.0
 *
 * @param string $plugin_slug The string identifier for the plugin in questions slug.
 * @return array{name: string, slug: string, short_description: string, requires: string|false, requires_php: string|false, requires_plugins: string[], version: string}|WP_Error Array of plugin data or WP_Error if failed.
 */
function perflab_query_plugin_info( string $plugin_slug ) {
	$transient_key = 'perflab_plugins_info';
	$plugins       = get_transient( $transient_key );

	if ( is_array( $plugins ) && isset( $plugins[ $plugin_slug ] ) ) {
		if ( isset( $plugins[ $plugin_slug ]['error'] ) ) {
			// Plugin was requested before but an error occurred for it.
			return new WP_Error(
				$plugins[ $plugin_slug ]['error']['code'],
				$plugins[ $plugin_slug ]['error']['message']
			);
		}
		return $plugins[ $plugin_slug ]; // Return cached plugin info if found.
	}

	$fields = array(
		'name',
		'slug',
		'short_description',
		'requires',
		'requires_php',
		'requires_plugins',
		'version', // Needed by install_plugin_install_status().
	);

	// Proceed with API request since no cache hit.
	$response = plugins_api(
		'query_plugins',
		array(
			'author'   => 'wordpressdotorg',
			'tag'      => 'performance',
			'per_page' => 100,
			'fields'   => array_fill_keys( $fields, true ),
		)
	);

	$has_errors = false;
	$plugins    = array();

	if ( is_wp_error( $response ) ) {
		$plugins[ $plugin_slug ] = array(
			'error' => array(
				'code'    => 'api_error',
				'message' => sprintf(
					/* translators: %s: API error message */
					__( 'Failed to retrieve plugins data from WordPress.org API: %s', 'performance-lab' ),
					$response->get_error_message()
				),
			),
		);

		foreach ( perflab_get_standalone_plugins() as $standalone_plugin ) {
			$plugins[ $standalone_plugin ] = $plugins[ $plugin_slug ];
		}

		$has_errors = true;
	} elseif ( ! is_object( $response ) || ! property_exists( $response, 'plugins' ) ) {
		$plugins[ $plugin_slug ] = array(
			'error' => array(
				'code'    => 'no_plugins',
				'message' => __( 'No plugins found in the API response.', 'performance-lab' ),
			),
		);

		foreach ( perflab_get_standalone_plugins() as $standalone_plugin ) {
			$plugins[ $standalone_plugin ] = $plugins[ $plugin_slug ];
		}

		$has_errors = true;
	} else {
		$plugin_queue = perflab_get_standalone_plugins();

		// Index the plugins from the API response by their slug for efficient lookup.
		$all_performance_plugins = array_column( $response->plugins, null, 'slug' );

		// Start processing the plugins using a queue-based approach.
		while ( count( $plugin_queue ) > 0 ) { // phpcs:ignore Squiz.PHP.DisallowSizeFunctionsInLoops.Found
			$current_plugin_slug = array_shift( $plugin_queue );

			// Skip already-processed plugins.
			if ( isset( $plugins[ $current_plugin_slug ] ) ) {
				continue;
			}

			if ( ! isset( $all_performance_plugins[ $current_plugin_slug ] ) ) {
				// Cache the fact that the plugin was not found.
				$plugins[ $current_plugin_slug ] = array(
					'error' => array(
						'code'    => 'plugin_not_found',
						'message' => __( 'Plugin not found in API response.', 'performance-lab' ),
					),
				);

				$has_errors = true;
			} else {
				$plugin_data                     = $all_performance_plugins[ $current_plugin_slug ];
				$plugins[ $current_plugin_slug ] = wp_array_slice_assoc( $plugin_data, $fields );

				// Enqueue the required plugins slug by adding it to the queue.
				if ( isset( $plugin_data['requires_plugins'] ) && is_array( $plugin_data['requires_plugins'] ) ) {
					$plugin_queue = array_merge( $plugin_queue, $plugin_data['requires_plugins'] );
				}
			}
		}

		if ( ! isset( $plugins[ $plugin_slug ] ) ) {
			// Cache the fact that the plugin was not found.
			$plugins[ $plugin_slug ] = array(
				'error' => array(
					'code'    => 'plugin_not_found',
					'message' => __( 'The requested plugin is not part of Performance Lab plugins.', 'performance-lab' ),
				),
			);

			$has_errors = true;
		}
	}

	set_transient( $transient_key, $plugins, $has_errors ? MINUTE_IN_SECONDS : HOUR_IN_SECONDS );

	if ( isset( $plugins[ $plugin_slug ]['error'] ) ) {
		return new WP_Error(
			$plugins[ $plugin_slug ]['error']['code'],
			$plugins[ $plugin_slug ]['error']['message']
		);
	}

	/**
	 * Validated (mostly) plugin data.
	 *
	 * @var array<string, array{name: string, slug: string, short_description: string, requires: string|false, requires_php: string|false, requires_plugins: string[], version: string}> $plugins
	 */
	return $plugins[ $plugin_slug ];
}

/**
 * Returns an array of WPP standalone plugins.
 *
 * @since 2.8.0
 *
 * @return string[] List of WPP standalone plugins as slugs.
 */
function perflab_get_standalone_plugins(): array {
	return array_keys(
		perflab_get_standalone_plugin_data()
	);
}

/**
 * Renders plugin UI for managing standalone plugins within PL Settings screen.
 *
 * @since 2.8.0
 */
function perflab_render_plugins_ui(): void {
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$plugins = array();
	$errors  = array();

	$standalone_plugin_data = perflab_get_standalone_plugin_data();
	foreach ( $standalone_plugin_data as $plugin_slug => $plugin_data ) {
		$api_data = perflab_query_plugin_info( $plugin_slug ); // Data from wordpress.org.

		// Skip if the plugin is not on WordPress.org or there was a network error.
		if ( $api_data instanceof WP_Error ) {
			$errors[ $plugin_slug ] = $api_data;
		} else {
			$plugins[ $plugin_slug ] = array_merge(
				array(
					'experimental' => false,
				),
				$plugin_data, // Data defined within Performance Lab.
				$api_data
			);
		}
	}

	if ( count( $errors ) > 0 ) {
		$active_plugins = array_map(
			static function ( string $file ) {
				return strtok( $file, '/' );
			},
			array_keys( get_plugins() )
		);
		$plugin_list    = '<ul>';
		$error_messages = array();
		foreach ( $errors as $plugin_slug => $error ) {
			if ( defined( $standalone_plugin_data[ $plugin_slug ]['constant'] ) ) {
				$status = __( '(active)', 'performance-lab' );
			} elseif ( in_array( $plugin_slug, $active_plugins, true ) ) {
				$status = __( '(installed)', 'performance-lab' );
			} else {
				$status = '';
			}

			$plugin_list     .= sprintf(
				'<li><a target="_blank" href="%s"><code>%s</code></a> %s</li>',
				esc_url( trailingslashit( __( 'https://wordpress.org/plugins/', 'default' ) . $plugin_slug ) ),
				esc_html( $plugin_slug ),
				esc_html( $status )
			);
			$error_messages[] = $error->get_error_message();
		}
		$plugin_list .= '</ul>';

		$error_messages = array_unique( $error_messages );

		if ( count( $error_messages ) === 1 ) {
			$error_text          = __( 'Failed to query WordPress.org Plugin Directory for the following plugin:', 'performance-lab' );
			$error_occurred_text = __( 'The following error occurred:', 'performance-lab' );
		} else {
			$error_text          = __( 'Failed to query WordPress.org Plugin Directory for the following plugins:', 'performance-lab' );
			$error_occurred_text = __( 'The following errors occurred:', 'performance-lab' );
		}

		wp_admin_notice(
			'<p>' . esc_html( $error_text ) . '</p>' .
			$plugin_list .
			'<p>' . esc_html( $error_occurred_text ) . '</p>' .
			'<ul><li>' .
			join(
				'</li><li>',
				array_map(
					static function ( string $error_message ): string {
						return wp_kses( $error_message, array( 'a' => array( 'href' => true ) ) );
					},
					$error_messages
				)
			)
			. '</li></ul>' .
			'<p>' . esc_html__( 'Please consider manual plugin installation and activation. You can then access each plugin\'s settings via its respective "Settings" link on the Plugins screen.', 'performance-lab' ) . '</p>',
			array(
				'type'           => 'error',
				'paragraph_wrap' => false,
			)
		);
	}

	/*
	 * Sort plugins alphabetically, with experimental ones coming last.
	 * Even though `experimental` is a boolean flag, the underlying
	 * algorithm (`usort` with `strcmp`) makes it possible to sort by it.
	 */
	$plugins = wp_list_sort(
		$plugins,
		array(
			'experimental' => 'ASC',
			'name'         => 'ASC',
		)
	);
	if ( count( $plugins ) === 0 ) {
		return;
	}
	?>
	<div class="wrap plugin-install-php">
		<h1><?php esc_html_e( 'Performance Features', 'performance-lab' ); ?></h1>
		<div class="wrap">
			<form id="plugin-filter" method="post">
				<div class="wp-list-table widefat plugin-install wpp-standalone-plugins">
					<h2 class="screen-reader-text"><?php esc_html_e( 'Plugins list', 'default' ); ?></h2>
					<div id="the-list">
						<?php
						foreach ( $plugins as $plugin_data ) {
							perflab_render_plugin_card( $plugin_data );
						}
						?>
					</div>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<?php
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<p>
			<?php
			$plugins_url = add_query_arg(
				array(
					's'             => 'WordPress Performance Team',
					'plugin_status' => 'all',
				),
				admin_url( 'plugins.php' )
			);
			echo wp_kses(
				sprintf(
					/* translators: %s is the URL to the plugins screen */
					__( 'Performance features are installed as plugins. To update features or remove them, <a href="%s">manage them on the plugins screen</a>.', 'performance-lab' ),
					esc_url( $plugins_url )
				),
				array(
					'a' => array( 'href' => true ),
				)
			);
			?>
		</p>
		<?php
	}
}

/**
 * Checks if a given plugin is available.
 *
 * @since 3.1.0
 * @see perflab_install_and_activate_plugin()
 *
 * @param array{name: string, slug: string, short_description: string, requires_php: string|false, requires: string|false, requires_plugins: string[], version: string} $plugin_data                     Plugin data from the WordPress.org API.
 * @param array<string, array{compatible_php: bool, compatible_wp: bool, can_install: bool, can_activate: bool, activated: bool, installed: bool}>                      $processed_plugin_availabilities Plugin availabilities already processed. This param is only used by recursive calls.
 * @return array{compatible_php: bool, compatible_wp: bool, can_install: bool, can_activate: bool, activated: bool, installed: bool} Availability.
 */
function perflab_get_plugin_availability( array $plugin_data, array &$processed_plugin_availabilities = array() ): array {
	if ( array_key_exists( $plugin_data['slug'], $processed_plugin_availabilities ) ) {
		// Prevent infinite recursion by returning the previously-computed value.
		return $processed_plugin_availabilities[ $plugin_data['slug'] ];
	}

	$availability = array(
		'compatible_php' => (
			false === $plugin_data['requires_php'] ||
			is_php_version_compatible( $plugin_data['requires_php'] )
		),
		'compatible_wp'  => (
			false === $plugin_data['requires'] ||
			is_wp_version_compatible( $plugin_data['requires'] )
		),
	);

	$plugin_status = install_plugin_install_status( $plugin_data );

	$availability['installed'] = ( 'install' !== $plugin_status['status'] );
	$availability['activated'] = false !== $plugin_status['file'] && is_plugin_active( $plugin_status['file'] );

	// The plugin is already installed or the user can install plugins.
	$availability['can_install'] = (
		$availability['installed'] ||
		current_user_can( 'install_plugins' )
	);

	// The plugin is activated or the user can activate plugins.
	$availability['can_activate'] = (
		$availability['activated'] ||
		false !== $plugin_status['file'] // When not false, the plugin is installed.
			? current_user_can( 'activate_plugin', $plugin_status['file'] )
			: current_user_can( 'activate_plugins' )
	);

	// Store pending availability before recursing.
	$processed_plugin_availabilities[ $plugin_data['slug'] ] = $availability;

	foreach ( $plugin_data['requires_plugins'] as $requires_plugin ) {
		$dependency_plugin_data = perflab_query_plugin_info( $requires_plugin );
		if ( $dependency_plugin_data instanceof WP_Error ) {
			continue;
		}

		$dependency_availability = perflab_get_plugin_availability( $dependency_plugin_data );
		foreach ( array( 'compatible_php', 'compatible_wp', 'can_install', 'can_activate', 'installed', 'activated' ) as $key ) {
			$availability[ $key ] = $availability[ $key ] && $dependency_availability[ $key ];
		}
	}

	$processed_plugin_availabilities[ $plugin_data['slug'] ] = $availability;
	return $availability;
}

/**
 * Installs and activates a plugin by its slug.
 *
 * Dependencies are recursively installed and activated as well.
 *
 * @since 3.1.0
 * @see perflab_get_plugin_availability()
 *
 * @param string   $plugin_slug       Plugin slug.
 * @param string[] $processed_plugins Slugs for plugins which have already been processed. This param is only used by recursive calls.
 * @return WP_Error|null WP_Error on failure.
 */
function perflab_install_and_activate_plugin( string $plugin_slug, array &$processed_plugins = array() ): ?WP_Error {
	if ( in_array( $plugin_slug, $processed_plugins, true ) ) {
		// Prevent infinite recursion from possible circular dependency.
		return null;
	}
	$processed_plugins[] = $plugin_slug;

	// Get the freshest data (including the most recent download_link) as opposed what is cached by perflab_query_plugin_info().
	$plugin_data = plugins_api(
		'plugin_information',
		array(
			'slug'   => $plugin_slug,
			'fields' => array(
				'download_link'    => true,
				'requires_plugins' => true,
				'sections'         => false, // Omit the bulk of the response which we don't need.
			),
		)
	);

	if ( $plugin_data instanceof WP_Error ) {
		return $plugin_data;
	}

	if ( is_object( $plugin_data ) ) {
		$plugin_data = (array) $plugin_data;
	}

	// Add recommended plugins (soft dependencies) to the list of plugins installed and activated.
	if ( 'embed-optimizer' === $plugin_slug ) {
		$plugin_data['requires_plugins'][] = 'optimization-detective';
	}

	// Install and activate plugin dependencies first.
	foreach ( $plugin_data['requires_plugins'] as $requires_plugin_slug ) {
		$result = perflab_install_and_activate_plugin( $requires_plugin_slug );
		if ( $result instanceof WP_Error ) {
			return $result;
		}
	}

	// Install the plugin.
	$plugin_status = install_plugin_install_status( $plugin_data );
	$plugin_file   = $plugin_status['file'];
	if ( 'install' === $plugin_status['status'] ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new WP_Error( 'cannot_install_plugin', __( 'Sorry, you are not allowed to install plugins on this site.', 'default' ) );
		}

		// Replace new Plugin_Installer_Skin with new Quiet_Upgrader_Skin when output needs to be suppressed.
		$skin     = new WP_Ajax_Upgrader_Skin( array( 'api' => $plugin_data ) );
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $plugin_data['download_link'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		} elseif ( is_wp_error( $skin->result ) ) {
			return $skin->result;
		} elseif ( $skin->get_errors()->has_errors() ) {
			return $skin->get_errors();
		}

		$plugins = get_plugins( '/' . $plugin_slug );
		if ( count( $plugins ) === 0 ) {
			return new WP_Error(
				'plugin_not_found',
				__( 'Plugin not found among installed plugins.', 'performance-lab' )
			);
		}

		$plugin_file_names = array_keys( $plugins );
		$plugin_file       = $plugin_slug . '/' . $plugin_file_names[0];
	}

	// Activate the plugin.
	if ( ! is_plugin_active( $plugin_file ) ) {
		if ( ! current_user_can( 'activate_plugin', $plugin_file ) ) {
			return new WP_Error( 'cannot_activate_plugin', __( 'Sorry, you are not allowed to activate this plugin.', 'default' ) );
		}

		$result = activate_plugin( $plugin_file );
		if ( $result instanceof WP_Error ) {
			return $result;
		}
	}

	return null;
}

/**
 * Renders individual plugin cards.
 *
 * This is adapted from `WP_Plugin_Install_List_Table::display_rows()` in core.
 *
 * @since 2.8.0
 *
 * @see WP_Plugin_Install_List_Table::display_rows()
 * @link https://github.com/WordPress/wordpress-develop/blob/0b8ca16ea3bd9722bd1a38f8ab68901506b1a0e7/src/wp-admin/includes/class-wp-plugin-install-list-table.php#L467-L830
 *
 * @param array{name: string, slug: string, short_description: string, requires_php: string|false, requires: string|false, requires_plugins: string[], version: string, experimental: bool} $plugin_data Plugin data augmenting data from the WordPress.org API.
 */
function perflab_render_plugin_card( array $plugin_data ): void {

	$name        = wp_strip_all_tags( $plugin_data['name'] );
	$description = wp_strip_all_tags( $plugin_data['short_description'] );

	/** This filter is documented in wp-admin/includes/class-wp-plugin-install-list-table.php */
	$description = apply_filters( 'plugin_install_description', $description, $plugin_data );

	$availability   = perflab_get_plugin_availability( $plugin_data );
	$compatible_php = $availability['compatible_php'];
	$compatible_wp  = $availability['compatible_wp'];

	$action_links = array();

	if ( $availability['activated'] ) {
		$action_links[] = sprintf(
			'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
			esc_html( _x( 'Active', 'plugin', 'default' ) )
		);
	} elseif (
		$availability['compatible_php'] &&
		$availability['compatible_wp'] &&
		$availability['can_install'] &&
		$availability['can_activate']
	) {
		$url = esc_url_raw(
			add_query_arg(
				array(
					'action'   => 'perflab_install_activate_plugin',
					'_wpnonce' => wp_create_nonce( 'perflab_install_activate_plugin' ),
					'slug'     => $plugin_data['slug'],
				),
				admin_url( 'options-general.php' )
			)
		);

		$action_links[] = sprintf(
			'<a class="button perflab-install-active-plugin" href="%s" data-plugin-slug="%s">%s</a>',
			esc_url( $url ),
			esc_attr( $plugin_data['slug'] ),
			esc_html__( 'Activate', 'default' )
		);
	} else {
		$explanation    = $availability['can_install'] ? _x( 'Cannot Activate', 'plugin', 'default' ) : _x( 'Cannot Install', 'plugin', 'default' );
		$action_links[] = sprintf(
			'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
			esc_html( $explanation )
		);
	}

	if ( current_user_can( 'install_plugins' ) ) {
		$title_link_attr = ' class="thickbox open-plugin-details-modal"';
		$details_link    = esc_url_raw(
			add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => $plugin_data['slug'],
					'TB_iframe' => 'true',
					'width'     => 600,
					'height'    => 550,
				),
				admin_url( 'plugin-install.php' )
			)
		);

		$action_links[] = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
			esc_url( $details_link ),
			/* translators: %s: Plugin name and version. */
			esc_attr( sprintf( __( 'More information about %s', 'default' ), $name ) ),
			esc_attr( $name ),
			esc_html__( 'Learn more', 'performance-lab' )
		);
	} else {
		$title_link_attr = ' target="_blank"';

		/* translators: %s: Plugin name. */
		$aria_label = sprintf( __( 'Visit plugin site for %s', 'default' ), $name );

		$details_link = __( 'https://wordpress.org/plugins/', 'default' ) . $plugin_data['slug'] . '/';

		$action_links[] = sprintf(
			'<a href="%s" aria-label="%s" target="_blank">%s</a>',
			esc_url( $details_link ),
			esc_attr( $aria_label ),
			esc_html__( 'Visit plugin site', 'default' )
		);
	}

	if ( $availability['activated'] ) {
		$settings_url = perflab_get_plugin_settings_url( $plugin_data['slug'] );
		if ( null !== $settings_url ) {
			/* translators: %s is the settings URL */
			$action_links[] = sprintf( '<a href="%s">%s</a>', esc_url( $settings_url ), esc_html__( 'Settings', 'performance-lab' ) );
		}
	}
	?>
	<div class="plugin-card plugin-card-<?php echo sanitize_html_class( $plugin_data['slug'] ); ?>">
		<?php
		if ( ! $compatible_php || ! $compatible_wp ) {
			echo '<div class="notice inline notice-error notice-alt">';
			if ( ! $compatible_php && ! $compatible_wp ) {
				echo '<p>' . esc_html_e( 'This plugin does not work with your versions of WordPress and PHP.', 'default' ) . '</p>';
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					echo wp_kses_post(
						sprintf(
							/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
							' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.', 'default' ),
							esc_url( self_admin_url( 'update-core.php' ) ),
							esc_url( wp_get_update_php_url() )
						)
					);
					wp_update_php_annotation( '<p><em>', '</em></p>' );
				} elseif ( current_user_can( 'update_core' ) ) {
					echo wp_kses_post(
						sprintf(
							/* translators: %s: URL to WordPress Updates screen. */
							' ' . __( '<a href="%s">Please update WordPress</a>.', 'default' ),
							esc_url( self_admin_url( 'update-core.php' ) )
						)
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					echo wp_kses_post(
						sprintf(
							/* translators: %s: URL to Update PHP page. */
							' ' . __( '<a href="%s">Learn more about updating PHP</a>.', 'default' ),
							esc_url( wp_get_update_php_url() )
						)
					);
					wp_update_php_annotation( '<p><em>', '</em></p>' );
				}
			} elseif ( ! $compatible_wp ) {
				esc_html_e( 'This plugin does not work with your version of WordPress.', 'default' );
				if ( current_user_can( 'update_core' ) ) {
					echo wp_kses_post(
						sprintf(
							/* translators: %s: URL to WordPress Updates screen. */
							' ' . __( '<a href="%s">Please update WordPress</a>.', 'default' ),
							esc_url( self_admin_url( 'update-core.php' ) )
						)
					);
				}
			} elseif ( ! $compatible_php ) {
				esc_html_e( 'This plugin does not work with your version of PHP.', 'default' );
				if ( current_user_can( 'update_php' ) ) {
					echo wp_kses_post(
						sprintf(
							/* translators: %s: URL to Update PHP page. */
							' ' . __( '<a href="%s">Learn more about updating PHP</a>.', 'default' ),
							esc_url( wp_get_update_php_url() )
						)
					);
					wp_update_php_annotation( '<p><em>', '</em></p>' );
				}
			}
			echo '</div>';
		}
		?>
		<div class="plugin-card-top">
			<div class="name column-name">
				<h3>
					<a href="<?php echo esc_url( $details_link ); ?>"<?php echo $title_link_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo wp_kses_post( $name ); ?>
					</a>
					<?php if ( $plugin_data['experimental'] ) : ?>
						<em class="perflab-plugin-experimental">
							<?php echo esc_html( _x( '(experimental)', 'plugin suffix', 'performance-lab' ) ); ?>
						</em>
					<?php endif; ?>
				</h3>
			</div>
			<div class="action-links">
				<ul class="plugin-action-buttons">
					<?php foreach ( $action_links as $action_link ) : ?>
						<li><?php echo wp_kses_post( $action_link ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="desc column-description">
				<p><?php echo wp_kses_post( $description ); ?></p>
			</div>
		</div>
	</div>
	<?php
}
