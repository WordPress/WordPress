<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 * @since   1.7.0
 */

/**
 * Base class for handling plugin conflicts.
 */
class Yoast_Plugin_Conflict {

	/**
	 * The plugins must be grouped per section.
	 *
	 * It's possible to check for each section if there are conflicting plugins.
	 *
	 * @var array
	 */
	protected $plugins = [];

	/**
	 * All the current active plugins will be stored in this private var.
	 *
	 * @var array
	 */
	protected $all_active_plugins = [];

	/**
	 * After searching for active plugins that are in $this->plugins the active plugins will be stored in this
	 * property.
	 *
	 * @var array
	 */
	protected $active_conflicting_plugins = [];

	/**
	 * Property for holding instance of itself.
	 *
	 * @var Yoast_Plugin_Conflict
	 */
	protected static $instance;

	/**
	 * For the use of singleton pattern. Create instance of itself and return this instance.
	 *
	 * @param string $class_name Give the classname to initialize. If classname is
	 *                           false (empty) it will use it's own __CLASS__.
	 *
	 * @return Yoast_Plugin_Conflict
	 */
	public static function get_instance( $class_name = '' ) {

		if ( self::$instance === null ) {
			if ( ! is_string( $class_name ) || $class_name === '' ) {
				$class_name = self::class;
			}

			self::$instance = new $class_name();
		}

		return self::$instance;
	}

	/**
	 * Setting instance, all active plugins and search for active plugins.
	 *
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside this class.
	 */
	protected function __construct() {
		// Set active plugins.
		$this->all_active_plugins = get_option( 'active_plugins' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['action'] ) && is_string( $_GET['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information and only comparing the variable in a condition.
			$action = wp_unslash( $_GET['action'] );
			if ( $action === 'deactivate' ) {
				$this->remove_deactivated_plugin();
			}
		}

		// Search for active plugins.
		$this->search_active_plugins();
	}

	/**
	 * Check if there are conflicting plugins for given $plugin_section.
	 *
	 * @param string $plugin_section Type of plugin conflict (such as Open Graph or sitemap).
	 *
	 * @return bool
	 */
	public function check_for_conflicts( $plugin_section ) {

		static $sections_checked;

		// Return early if there are no active conflicting plugins at all.
		if ( empty( $this->active_conflicting_plugins ) ) {
			return false;
		}

		if ( $sections_checked === null ) {
			$sections_checked = [];
		}

		if ( ! in_array( $plugin_section, $sections_checked, true ) ) {
			$sections_checked[] = $plugin_section;
			return ( ! empty( $this->active_conflicting_plugins[ $plugin_section ] ) );
		}

		return false;
	}

	/**
	 * Checks for given $plugin_sections for conflicts.
	 *
	 * @param array $plugin_sections Set of sections.
	 *
	 * @return void
	 */
	public function check_plugin_conflicts( $plugin_sections ) {
		foreach ( $plugin_sections as $plugin_section => $readable_plugin_section ) {
			// Check for conflicting plugins and show error if there are conflicts.
			if ( $this->check_for_conflicts( $plugin_section ) ) {
				$this->set_error( $plugin_section, $readable_plugin_section );
			}
		}

		// List of all active sections.
		$sections = array_keys( $plugin_sections );
		// List of all sections.
		$all_plugin_sections = array_keys( $this->plugins );

		/*
		 * Get all sections that are inactive.
		 * These plugins need to be cleared.
		 *
		 * This happens when Sitemaps or OpenGraph implementations toggle active/disabled.
		 */
		$inactive_sections = array_diff( $all_plugin_sections, $sections );
		if ( ! empty( $inactive_sections ) ) {
			foreach ( $inactive_sections as $section ) {
				array_walk( $this->plugins[ $section ], [ $this, 'clear_error' ] );
			}
		}

		// For active sections clear errors for inactive plugins.
		foreach ( $sections as $section ) {
			// By default, clear errors for all plugins of the section.
			$inactive_plugins = $this->plugins[ $section ];

			// If there are active plugins, filter them from being cleared.
			if ( isset( $this->active_conflicting_plugins[ $section ] ) ) {
				$inactive_plugins = array_diff( $this->plugins[ $section ], $this->active_conflicting_plugins[ $section ] );
			}

			array_walk( $inactive_plugins, [ $this, 'clear_error' ] );
		}
	}

	/**
	 * Setting an error on the screen.
	 *
	 * @param string $plugin_section          Type of conflict group (such as Open Graph or sitemap).
	 * @param string $readable_plugin_section This is the value for the translation.
	 *
	 * @return void
	 */
	protected function set_error( $plugin_section, $readable_plugin_section ) {

		$notification_center = Yoast_Notification_Center::get();

		foreach ( $this->active_conflicting_plugins[ $plugin_section ] as $plugin_file ) {

			$plugin_name = $this->get_plugin_name( $plugin_file );

			$error_message = '';
			/* translators: %1$s: 'Facebook & Open Graph' plugin name(s) of possibly conflicting plugin(s), %2$s to Yoast SEO */
			$error_message .= '<p>' . sprintf( __( 'The %1$s plugin might cause issues when used in conjunction with %2$s.', 'wordpress-seo' ), '<em>' . $plugin_name . '</em>', 'Yoast SEO' ) . '</p>';
			$error_message .= '<p>' . sprintf( $readable_plugin_section, 'Yoast SEO', $plugin_name ) . '</p>';

			/* translators: %s: 'Facebook' plugin name of possibly conflicting plugin */
			$error_message .= '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all', 'deactivate-plugin_' . $plugin_file ) . '">' . sprintf( __( 'Deactivate %s', 'wordpress-seo' ), $this->get_plugin_name( $plugin_file ) ) . '</a> ';

			$identifier = $this->get_notification_identifier( $plugin_file );

			// Add the message to the notifications center.
			$notification_center->add_notification(
				new Yoast_Notification(
					$error_message,
					[
						'type' => Yoast_Notification::ERROR,
						'id'   => 'wpseo-conflict-' . $identifier,
					]
				)
			);
		}
	}

	/**
	 * Clear the notification for a plugin.
	 *
	 * @param string $plugin_file Clear the optional notification for this plugin.
	 *
	 * @return void
	 */
	public function clear_error( $plugin_file ) {
		$identifier = $this->get_notification_identifier( $plugin_file );

		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification_by_id( 'wpseo-conflict-' . $identifier );
	}

	/**
	 * Loop through the $this->plugins to check if one of the plugins is active.
	 *
	 * This method will store the active plugins in $this->active_plugins.
	 *
	 * @return void
	 */
	protected function search_active_plugins() {
		foreach ( $this->plugins as $plugin_section => $plugins ) {
			$this->check_plugins_active( $plugins, $plugin_section );
		}
	}

	/**
	 * Loop through plugins and check if each plugin is active.
	 *
	 * @param array  $plugins        Set of plugins.
	 * @param string $plugin_section Type of conflict group (such as Open Graph or sitemap).
	 *
	 * @return void
	 */
	protected function check_plugins_active( $plugins, $plugin_section ) {
		foreach ( $plugins as $plugin ) {
			if ( $this->check_plugin_is_active( $plugin ) ) {
				$this->add_active_plugin( $plugin_section, $plugin );
			}
		}
	}

	/**
	 * Check if given plugin exists in array with all_active_plugins.
	 *
	 * @param string $plugin Plugin basename string.
	 *
	 * @return bool
	 */
	protected function check_plugin_is_active( $plugin ) {
		return in_array( $plugin, $this->all_active_plugins, true );
	}

	/**
	 * Add plugin to the list of active plugins.
	 *
	 * This method will check first if key $plugin_section exists, if not it will create an empty array
	 * If $plugin itself doesn't exist it will be added.
	 *
	 * @param string $plugin_section Type of conflict group (such as Open Graph or sitemap).
	 * @param string $plugin         Plugin basename string.
	 *
	 * @return void
	 */
	protected function add_active_plugin( $plugin_section, $plugin ) {
		if ( ! array_key_exists( $plugin_section, $this->active_conflicting_plugins ) ) {
			$this->active_conflicting_plugins[ $plugin_section ] = [];
		}

		if ( ! in_array( $plugin, $this->active_conflicting_plugins[ $plugin_section ], true ) ) {
			$this->active_conflicting_plugins[ $plugin_section ][] = $plugin;
		}
	}

	/**
	 * Search in $this->plugins for the given $plugin.
	 *
	 * If there is a result it will return the plugin category.
	 *
	 * @param string $plugin Plugin basename string.
	 *
	 * @return int|string
	 */
	protected function find_plugin_category( $plugin ) {
		foreach ( $this->plugins as $plugin_section => $plugins ) {
			if ( in_array( $plugin, $plugins, true ) ) {
				return $plugin_section;
			}
		}
	}

	/**
	 * Get plugin name from file.
	 *
	 * @param string $plugin Plugin path relative to plugins directory.
	 *
	 * @return string|bool Plugin name or false when no name is set.
	 */
	protected function get_plugin_name( $plugin ) {
		$plugin_details = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );

		if ( $plugin_details['Name'] !== '' ) {
			return $plugin_details['Name'];
		}

		return false;
	}

	/**
	 * When being in the deactivation process the currently deactivated plugin has to be removed.
	 *
	 * @return void
	 */
	private function remove_deactivated_plugin() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: On the deactivation screen the nonce is already checked by WordPress itself.
		if ( ! isset( $_GET['plugin'] ) || ! is_string( $_GET['plugin'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: On the deactivation screen the nonce is already checked by WordPress itself.
		$deactivated_plugin = sanitize_text_field( wp_unslash( $_GET['plugin'] ) );
		$key_to_remove      = array_search( $deactivated_plugin, $this->all_active_plugins, true );

		if ( $key_to_remove !== false ) {
			unset( $this->all_active_plugins[ $key_to_remove ] );
		}
	}

	/**
	 * Get the identifier from the plugin file.
	 *
	 * @param string $plugin_file Plugin file to get Identifier from.
	 *
	 * @return string
	 */
	private function get_notification_identifier( $plugin_file ) {
		return md5( $plugin_file );
	}
}
