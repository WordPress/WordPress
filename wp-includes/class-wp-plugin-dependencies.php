<?php
/**
 * WordPress Plugin Administration API: WP_Plugin_Dependencies class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 6.5.0
 */

/**
 * Core class for installing plugin dependencies.
 *
 * It is designed to add plugin dependencies as designated in the
 * `Requires Plugins` header to a new view in the plugins install page.
 */
class WP_Plugin_Dependencies {

	/**
	 * Holds 'get_plugins()'.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	protected static $plugins;

	/**
	 * Holds plugin directory names to compare with cache.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	protected static $plugin_dirnames;

	/**
	 * Holds sanitized plugin dependency slugs.
	 *
	 * Keyed on the dependent plugin's filepath,
	 * relative to the plugins directory.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	protected static $dependencies;

	/**
	 * Holds an array of sanitized plugin dependency slugs.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	protected static $dependency_slugs;

	/**
	 * Holds an array of dependent plugin slugs.
	 *
	 * Keyed on the dependent plugin's filepath,
	 * relative to the plugins directory.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	protected static $dependent_slugs;

	/**
	 * Holds 'plugins_api()' data for plugin dependencies.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	protected static $dependency_api_data;

	/**
	 * Holds plugin dependency filepaths, relative to the plugins directory.
	 *
	 * Keyed on the dependency's slug.
	 *
	 * @since 6.5.0
	 *
	 * @var string[]
	 */
	protected static $dependency_filepaths;

	/**
	 * An array of circular dependency pairings.
	 *
	 * @since 6.5.0
	 *
	 * @var array[]
	 */
	protected static $circular_dependencies_pairs;

	/**
	 * An array of circular dependency slugs.
	 *
	 * @since 6.5.0
	 *
	 * @var string[]
	 */
	protected static $circular_dependencies_slugs;

	/**
	 * Whether Plugin Dependencies have been initialized.
	 *
	 * @since 6.5.0
	 *
	 * @var bool
	 */
	protected static $initialized = false;

	/**
	 * Initializes by fetching plugin header and plugin API data.
	 *
	 * @since 6.5.0
	 */
	public static function initialize() {
		if ( false === self::$initialized ) {
			self::read_dependencies_from_plugin_headers();
			self::get_dependency_api_data();
			self::$initialized = true;
		}
	}

	/**
	 * Determines whether the plugin has plugins that depend on it.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return bool Whether the plugin has plugins that depend on it.
	 */
	public static function has_dependents( $plugin_file ) {
		return in_array( self::convert_to_slug( $plugin_file ), (array) self::$dependency_slugs, true );
	}

	/**
	 * Determines whether the plugin has plugin dependencies.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return bool Whether a plugin has plugin dependencies.
	 */
	public static function has_dependencies( $plugin_file ) {
		return isset( self::$dependencies[ $plugin_file ] );
	}

	/**
	 * Determines whether the plugin has active dependents.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return bool Whether the plugin has active dependents.
	 */
	public static function has_active_dependents( $plugin_file ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$dependents = self::get_dependents( self::convert_to_slug( $plugin_file ) );
		foreach ( $dependents as $dependent ) {
			if ( is_plugin_active( $dependent ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets filepaths of plugins that require the dependency.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug The dependency's slug.
	 * @return array An array of dependent plugin filepaths, relative to the plugins directory.
	 */
	public static function get_dependents( $slug ) {
		$dependents = array();

		foreach ( (array) self::$dependencies as $dependent => $dependencies ) {
			if ( in_array( $slug, $dependencies, true ) ) {
				$dependents[] = $dependent;
			}
		}

		return $dependents;
	}

	/**
	 * Gets the slugs of plugins that the dependent requires.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The dependent plugin's filepath, relative to the plugins directory.
	 * @return array An array of dependency plugin slugs.
	 */
	public static function get_dependencies( $plugin_file ) {
		if ( isset( self::$dependencies[ $plugin_file ] ) ) {
			return self::$dependencies[ $plugin_file ];
		}

		return array();
	}

	/**
	 * Gets a dependent plugin's filepath.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug  The dependent plugin's slug.
	 * @return string|false The dependent plugin's filepath, relative to the plugins directory,
	 *                      or false if the plugin has no dependencies.
	 */
	public static function get_dependent_filepath( $slug ) {
		$filepath = array_search( $slug, self::$dependent_slugs, true );

		return $filepath ? $filepath : false;
	}

	/**
	 * Determines whether the plugin has unmet dependencies.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return bool Whether the plugin has unmet dependencies.
	 */
	public static function has_unmet_dependencies( $plugin_file ) {
		if ( ! isset( self::$dependencies[ $plugin_file ] ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		foreach ( self::$dependencies[ $plugin_file ] as $dependency ) {
			$dependency_filepath = self::get_dependency_filepath( $dependency );

			if ( false === $dependency_filepath || is_plugin_inactive( $dependency_filepath ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines whether the plugin has a circular dependency.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return bool Whether the plugin has a circular dependency.
	 */
	public static function has_circular_dependency( $plugin_file ) {
		if ( ! is_array( self::$circular_dependencies_slugs ) ) {
			self::get_circular_dependencies();
		}

		if ( ! empty( self::$circular_dependencies_slugs ) ) {
			$slug = self::convert_to_slug( $plugin_file );

			if ( in_array( $slug, self::$circular_dependencies_slugs, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the names of plugins that require the plugin.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return array An array of dependent names.
	 */
	public static function get_dependent_names( $plugin_file ) {
		$dependent_names = array();
		$plugins         = self::get_plugins();
		$slug            = self::convert_to_slug( $plugin_file );

		foreach ( self::get_dependents( $slug ) as $dependent ) {
			$dependent_names[ $dependent ] = $plugins[ $dependent ]['Name'];
		}
		sort( $dependent_names );

		return $dependent_names;
	}

	/**
	 * Gets the names of plugins required by the plugin.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The dependent plugin's filepath, relative to the plugins directory.
	 * @return array An array of dependency names.
	 */
	public static function get_dependency_names( $plugin_file ) {
		$dependency_api_data = self::get_dependency_api_data();
		$dependencies        = self::get_dependencies( $plugin_file );
		$plugins             = self::get_plugins();

		$dependency_names = array();
		foreach ( $dependencies as $dependency ) {
			// Use the name if it's available, otherwise fall back to the slug.
			if ( isset( $dependency_api_data[ $dependency ]['name'] ) ) {
				$name = $dependency_api_data[ $dependency ]['name'];
			} else {
				$dependency_filepath = self::get_dependency_filepath( $dependency );
				if ( false !== $dependency_filepath ) {
					$name = $plugins[ $dependency_filepath ]['Name'];
				} else {
					$name = $dependency;
				}
			}

			$dependency_names[ $dependency ] = $name;
		}

		return $dependency_names;
	}

	/**
	 * Gets the filepath for a dependency, relative to the plugin's directory.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug The dependency's slug.
	 * @return string|false If installed, the dependency's filepath relative to the plugins directory, otherwise false.
	 */
	public static function get_dependency_filepath( $slug ) {
		$dependency_filepaths = self::get_dependency_filepaths();

		if ( ! isset( $dependency_filepaths[ $slug ] ) ) {
			return false;
		}

		return $dependency_filepaths[ $slug ];
	}

	/**
	 * Returns API data for the dependency.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug The dependency's slug.
	 * @return array|false The dependency's API data on success, otherwise false.
	 */
	public static function get_dependency_data( $slug ) {
		$dependency_api_data = self::get_dependency_api_data();

		if ( isset( $dependency_api_data[ $slug ] ) ) {
			return $dependency_api_data[ $slug ];
		}

		return false;
	}

	/**
	 * Displays an admin notice if dependencies are not installed.
	 *
	 * @since 6.5.0
	 */
	public static function display_admin_notice_for_unmet_dependencies() {
		if ( in_array( false, self::get_dependency_filepaths(), true ) ) {
			$error_message = __( 'Some required plugins are missing or inactive.' );

			if ( is_multisite() ) {
				if ( current_user_can( 'manage_network_plugins' ) ) {
					$error_message .= ' ' . sprintf(
						/* translators: %s: Link to the network plugins page. */
						__( '<a href="%s">Manage plugins</a>.' ),
						esc_url( network_admin_url( 'plugins.php' ) )
					);
				} else {
					$error_message .= ' ' . __( 'Please contact your network administrator.' );
				}
			} elseif ( 'plugins' !== get_current_screen()->base ) {
				$error_message .= ' ' . sprintf(
					/* translators: %s: Link to the plugins page. */
					__( '<a href="%s">Manage plugins</a>.' ),
					esc_url( admin_url( 'plugins.php' ) )
				);
			}

			wp_admin_notice(
				$error_message,
				array(
					'type' => 'warning',
				)
			);
		}
	}

	/**
	 * Displays an admin notice if circular dependencies are installed.
	 *
	 * @since 6.5.0
	 */
	public static function display_admin_notice_for_circular_dependencies() {
		$circular_dependencies = self::get_circular_dependencies();
		if ( ! empty( $circular_dependencies ) && count( $circular_dependencies ) > 1 ) {
			$circular_dependencies = array_unique( $circular_dependencies, SORT_REGULAR );
			$plugins               = self::get_plugins();
			$plugin_dirnames       = self::get_plugin_dirnames();

			// Build output lines.
			$circular_dependency_lines = '';
			foreach ( $circular_dependencies as $circular_dependency ) {
				$first_filepath             = $plugin_dirnames[ $circular_dependency[0] ];
				$second_filepath            = $plugin_dirnames[ $circular_dependency[1] ];
				$circular_dependency_lines .= sprintf(
					/* translators: 1: First plugin name, 2: Second plugin name. */
					'<li>' . _x( '%1$s requires %2$s', 'The first plugin requires the second plugin.' ) . '</li>',
					'<strong>' . esc_html( $plugins[ $first_filepath ]['Name'] ) . '</strong>',
					'<strong>' . esc_html( $plugins[ $second_filepath ]['Name'] ) . '</strong>'
				);
			}

			wp_admin_notice(
				sprintf(
					'<p>%1$s</p><ul>%2$s</ul><p>%3$s</p>',
					__( 'These plugins cannot be activated because their requirements are invalid.' ),
					$circular_dependency_lines,
					__( 'Please contact the plugin authors for more information.' )
				),
				array(
					'type'           => 'warning',
					'paragraph_wrap' => false,
				)
			);
		}
	}

	/**
	 * Checks plugin dependencies after a plugin is installed via AJAX.
	 *
	 * @since 6.5.0
	 */
	public static function check_plugin_dependencies_during_ajax() {
		check_ajax_referer( 'updates' );

		if ( empty( $_POST['slug'] ) ) {
			wp_send_json_error(
				array(
					'slug'         => '',
					'pluginName'   => '',
					'errorCode'    => 'no_plugin_specified',
					'errorMessage' => __( 'No plugin specified.' ),
				)
			);
		}

		$slug   = sanitize_key( wp_unslash( $_POST['slug'] ) );
		$status = array( 'slug' => $slug );

		self::get_plugins();
		self::get_plugin_dirnames();

		if ( ! isset( self::$plugin_dirnames[ $slug ] ) ) {
			$status['errorCode']    = 'plugin_not_installed';
			$status['errorMessage'] = __( 'The plugin is not installed.' );
			wp_send_json_error( $status );
		}

		$plugin_file          = self::$plugin_dirnames[ $slug ];
		$status['pluginName'] = self::$plugins[ $plugin_file ]['Name'];
		$status['plugin']     = $plugin_file;

		if ( current_user_can( 'activate_plugin', $plugin_file ) && is_plugin_inactive( $plugin_file ) ) {
			$status['activateUrl'] = add_query_arg(
				array(
					'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin_file ),
					'action'   => 'activate',
					'plugin'   => $plugin_file,
				),
				is_multisite() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' )
			);
		}

		if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
			$status['activateUrl'] = add_query_arg( array( 'networkwide' => 1 ), $status['activateUrl'] );
		}

		self::initialize();
		$dependencies = self::get_dependencies( $plugin_file );
		if ( empty( $dependencies ) ) {
			$status['message'] = __( 'The plugin has no required plugins.' );
			wp_send_json_success( $status );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$inactive_dependencies = array();
		foreach ( $dependencies as $dependency ) {
			if ( false === self::$plugin_dirnames[ $dependency ] || is_plugin_inactive( self::$plugin_dirnames[ $dependency ] ) ) {
				$inactive_dependencies[] = $dependency;
			}
		}

		if ( ! empty( $inactive_dependencies ) ) {
			$inactive_dependency_names = array_map(
				function ( $dependency ) {
					if ( isset( self::$dependency_api_data[ $dependency ]['Name'] ) ) {
						$inactive_dependency_name = self::$dependency_api_data[ $dependency ]['Name'];
					} else {
						$inactive_dependency_name = $dependency;
					}
					return $inactive_dependency_name;
				},
				$inactive_dependencies
			);

			$status['errorCode']    = 'inactive_dependencies';
			$status['errorMessage'] = sprintf(
				/* translators: %s: A list of inactive dependency plugin names. */
				__( 'The following plugins must be activated first: %s.' ),
				implode( ', ', $inactive_dependency_names )
			);
			$status['errorData'] = array_combine( $inactive_dependencies, $inactive_dependency_names );

			wp_send_json_error( $status );
		}

		$status['message'] = __( 'All required plugins are installed and activated.' );
		wp_send_json_success( $status );
	}

	/**
	 * Gets data for installed plugins.
	 *
	 * @since 6.5.0
	 *
	 * @return array An array of plugin data.
	 */
	protected static function get_plugins() {
		if ( is_array( self::$plugins ) ) {
			return self::$plugins;
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$plugins = get_plugins();

		return self::$plugins;
	}

	/**
	 * Reads and stores dependency slugs from a plugin's 'Requires Plugins' header.
	 *
	 * @since 6.5.0
	 */
	protected static function read_dependencies_from_plugin_headers() {
		self::$dependencies     = array();
		self::$dependency_slugs = array();
		self::$dependent_slugs  = array();
		$plugins                = self::get_plugins();
		foreach ( $plugins as $plugin => $header ) {
			if ( '' === $header['RequiresPlugins'] ) {
				continue;
			}

			$dependency_slugs              = self::sanitize_dependency_slugs( $header['RequiresPlugins'] );
			self::$dependencies[ $plugin ] = $dependency_slugs;
			self::$dependency_slugs        = array_merge( self::$dependency_slugs, $dependency_slugs );

			$dependent_slug                   = self::convert_to_slug( $plugin );
			self::$dependent_slugs[ $plugin ] = $dependent_slug;
		}
		self::$dependency_slugs = array_unique( self::$dependency_slugs );
	}

	/**
	 * Sanitizes slugs.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slugs A comma-separated string of plugin dependency slugs.
	 * @return array An array of sanitized plugin dependency slugs.
	 */
	protected static function sanitize_dependency_slugs( $slugs ) {
		$sanitized_slugs = array();
		$slugs           = explode( ',', $slugs );

		foreach ( $slugs as $slug ) {
			$slug = trim( $slug );

			/**
			 * Filters a plugin dependency's slug before matching to
			 * the WordPress.org slug format.
			 *
			 * Can be used to switch between free and premium plugin slugs, for example.
			 *
			 * @since 6.5.0
			 *
			 * @param string $slug The slug.
			 */
			$slug = apply_filters( 'wp_plugin_dependencies_slug', $slug );

			// Match to WordPress.org slug format.
			if ( preg_match( '/^[a-z0-9]+(-[a-z0-9]+)*$/mu', $slug ) ) {
				$sanitized_slugs[] = $slug;
			}
		}
		$sanitized_slugs = array_unique( $sanitized_slugs );
		sort( $sanitized_slugs );

		return $sanitized_slugs;
	}

	/**
	 * Gets the filepath of installed dependencies.
	 * If a dependency is not installed, the filepath defaults to false.
	 *
	 * @since 6.5.0
	 *
	 * @return array An array of install dependencies filepaths, relative to the plugins directory.
	 */
	protected static function get_dependency_filepaths() {
		if ( is_array( self::$dependency_filepaths ) ) {
			return self::$dependency_filepaths;
		}

		if ( null === self::$dependency_slugs ) {
			return array();
		}

		self::$dependency_filepaths = array();

		$plugin_dirnames = self::get_plugin_dirnames();
		foreach ( self::$dependency_slugs as $slug ) {
			if ( isset( $plugin_dirnames[ $slug ] ) ) {
				self::$dependency_filepaths[ $slug ] = $plugin_dirnames[ $slug ];
				continue;
			}

			self::$dependency_filepaths[ $slug ] = false;
		}

		return self::$dependency_filepaths;
	}

	/**
	 * Retrieves and stores dependency plugin data from the WordPress.org Plugin API.
	 *
	 * @since 6.5.0
	 *
	 * @global string $pagenow The filename of the current screen.
	 *
	 * @return array|void An array of dependency API data, or void on early exit.
	 */
	protected static function get_dependency_api_data() {
		global $pagenow;

		if ( ! is_admin() || ( 'plugins.php' !== $pagenow && 'plugin-install.php' !== $pagenow ) ) {
			return;
		}

		if ( is_array( self::$dependency_api_data ) ) {
			return self::$dependency_api_data;
		}

		$plugins                   = self::get_plugins();
		self::$dependency_api_data = (array) get_site_transient( 'wp_plugin_dependencies_plugin_data' );
		foreach ( self::$dependency_slugs as $slug ) {
			// Set transient for individual data, remove from self::$dependency_api_data if transient expired.
			if ( ! get_site_transient( "wp_plugin_dependencies_plugin_timeout_{$slug}" ) ) {
				unset( self::$dependency_api_data[ $slug ] );
				set_site_transient( "wp_plugin_dependencies_plugin_timeout_{$slug}", true, 12 * HOUR_IN_SECONDS );
			}

			if ( isset( self::$dependency_api_data[ $slug ] ) ) {
				if ( false === self::$dependency_api_data[ $slug ] ) {
					$dependency_file = self::get_dependency_filepath( $slug );

					if ( false === $dependency_file ) {
						self::$dependency_api_data[ $slug ] = array( 'Name' => $slug );
					} else {
						self::$dependency_api_data[ $slug ] = array( 'Name' => $plugins[ $dependency_file ]['Name'] );
					}
					continue;
				}

				// Don't hit the Plugin API if data exists.
				if ( ! empty( self::$dependency_api_data[ $slug ]['last_updated'] ) ) {
					continue;
				}
			}

			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			$information = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'short_description' => true,
						'icons'             => true,
					),
				)
			);

			if ( is_wp_error( $information ) ) {
				continue;
			}

			self::$dependency_api_data[ $slug ] = (array) $information;
			// plugins_api() returns 'name' not 'Name'.
			self::$dependency_api_data[ $slug ]['Name'] = self::$dependency_api_data[ $slug ]['name'];
			set_site_transient( 'wp_plugin_dependencies_plugin_data', self::$dependency_api_data, 0 );
		}

		// Remove from self::$dependency_api_data if slug no longer a dependency.
		$differences = array_diff( array_keys( self::$dependency_api_data ), self::$dependency_slugs );
		foreach ( $differences as $difference ) {
			unset( self::$dependency_api_data[ $difference ] );
		}

		ksort( self::$dependency_api_data );
		// Remove empty elements.
		self::$dependency_api_data = array_filter( self::$dependency_api_data );
		set_site_transient( 'wp_plugin_dependencies_plugin_data', self::$dependency_api_data, 0 );

		return self::$dependency_api_data;
	}

	/**
	 * Gets plugin directory names.
	 *
	 * @since 6.5.0
	 *
	 * @return array An array of plugin directory names.
	 */
	protected static function get_plugin_dirnames() {
		if ( is_array( self::$plugin_dirnames ) ) {
			return self::$plugin_dirnames;
		}

		self::$plugin_dirnames = array();

		$plugin_files = array_keys( self::get_plugins() );
		foreach ( $plugin_files as $plugin_file ) {
			$slug                           = self::convert_to_slug( $plugin_file );
			self::$plugin_dirnames[ $slug ] = $plugin_file;
		}

		return self::$plugin_dirnames;
	}

	/**
	 * Gets circular dependency data.
	 *
	 * @since 6.5.0
	 *
	 * @return array[] An array of circular dependency pairings.
	 */
	protected static function get_circular_dependencies() {
		if ( is_array( self::$circular_dependencies_pairs ) ) {
			return self::$circular_dependencies_pairs;
		}

		if ( null === self::$dependencies ) {
			return array();
		}

		self::$circular_dependencies_slugs = array();

		self::$circular_dependencies_pairs = array();
		foreach ( self::$dependencies as $dependent => $dependencies ) {
			/*
			 * $dependent is in 'a/a.php' format. Dependencies are stored as slugs, i.e. 'a'.
			 *
			 * Convert $dependent to slug format for checking.
			 */
			$dependent_slug = self::convert_to_slug( $dependent );

			self::$circular_dependencies_pairs = array_merge(
				self::$circular_dependencies_pairs,
				self::check_for_circular_dependencies( array( $dependent_slug ), $dependencies )
			);
		}

		return self::$circular_dependencies_pairs;
	}

	/**
	 * Checks for circular dependencies.
	 *
	 * @since 6.5.0
	 *
	 * @param array $dependents   Array of dependent plugins.
	 * @param array $dependencies Array of plugins dependencies.
	 * @return array A circular dependency pairing, or an empty array if none exists.
	 */
	protected static function check_for_circular_dependencies( $dependents, $dependencies ) {
		$circular_dependencies_pairs = array();

		// Check for a self-dependency.
		$dependents_location_in_its_own_dependencies = array_intersect( $dependents, $dependencies );
		if ( ! empty( $dependents_location_in_its_own_dependencies ) ) {
			foreach ( $dependents_location_in_its_own_dependencies as $self_dependency ) {
				self::$circular_dependencies_slugs[] = $self_dependency;
				$circular_dependencies_pairs[]       = array( $self_dependency, $self_dependency );

				// No need to check for itself again.
				unset( $dependencies[ array_search( $self_dependency, $dependencies, true ) ] );
			}
		}

		/*
		 * Check each dependency to see:
		 * 1. If it has dependencies.
		 * 2. If its list of dependencies includes one of its own dependents.
		 */
		foreach ( $dependencies as $dependency ) {
			// Check if the dependency is also a dependent.
			$dependency_location_in_dependents = array_search( $dependency, self::$dependent_slugs, true );

			if ( false !== $dependency_location_in_dependents ) {
				$dependencies_of_the_dependency = self::$dependencies[ $dependency_location_in_dependents ];

				foreach ( $dependents as $dependent ) {
					// Check if its dependencies includes one of its own dependents.
					$dependent_location_in_dependency_dependencies = array_search(
						$dependent,
						$dependencies_of_the_dependency,
						true
					);

					if ( false !== $dependent_location_in_dependency_dependencies ) {
						self::$circular_dependencies_slugs[] = $dependent;
						self::$circular_dependencies_slugs[] = $dependency;
						$circular_dependencies_pairs[]       = array( $dependent, $dependency );

						// Remove the dependent from its dependency's dependencies.
						unset( $dependencies_of_the_dependency[ $dependent_location_in_dependency_dependencies ] );
					}
				}

				$dependents[] = $dependency;

				/*
				 * Now check the dependencies of the dependency's dependencies for the dependent.
				 *
				 * Yes, that does make sense.
				 */
				$circular_dependencies_pairs = array_merge(
					$circular_dependencies_pairs,
					self::check_for_circular_dependencies( $dependents, array_unique( $dependencies_of_the_dependency ) )
				);
			}
		}

		return $circular_dependencies_pairs;
	}

	/**
	 * Converts a plugin filepath to a slug.
	 *
	 * @since 6.5.0
	 *
	 * @param string $plugin_file The plugin's filepath, relative to the plugins directory.
	 * @return string The plugin's slug.
	 */
	protected static function convert_to_slug( $plugin_file ) {
		if ( 'hello.php' === $plugin_file ) {
			return 'hello-dolly';
		}
		return str_contains( $plugin_file, '/' ) ? dirname( $plugin_file ) : str_replace( '.php', '', $plugin_file );
	}
}
