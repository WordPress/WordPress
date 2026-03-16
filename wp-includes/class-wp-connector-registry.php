<?php
/**
 * Connectors API: WP_Connector_Registry class.
 *
 * @package WordPress
 * @subpackage Connectors
 * @since 7.0.0
 */

/**
 * Manages the registration and lookup of connectors.
 *
 * This is an internal class. Use the public API functions to interact with connectors:
 *
 *  - `wp_is_connector_registered()` — check if a connector exists.
 *  - `wp_get_connector()`           — retrieve a single connector's data.
 *  - `wp_get_connectors()`          — retrieve all registered connectors.
 *
 * Plugins receive the registry instance via the `wp_connectors_init` action
 * to register or override connectors directly.
 *
 * @since 7.0.0
 * @access private
 *
 * @see wp_is_connector_registered()
 * @see wp_get_connector()
 * @see wp_get_connectors()
 * @see _wp_connectors_init()
 *
 * @phpstan-type Connector array{
 *     name: non-empty-string,
 *     description: non-empty-string,
 *     logo_url?: non-empty-string,
 *     type: 'ai_provider',
 *     authentication: array{
 *         method: 'api_key'|'none',
 *         credentials_url?: non-empty-string,
 *         setting_name?: non-empty-string
 *     },
 *     plugin?: array{
 *         slug: non-empty-string
 *     }
 * }
 */
final class WP_Connector_Registry {
	/**
	 * The singleton instance of the registry.
	 *
	 * @since 7.0.0
	 */
	private static ?WP_Connector_Registry $instance = null;

	/**
	 * Holds the registered connectors.
	 *
	 * Each connector is stored as an associative array with keys:
	 * name, description, type, authentication, and optionally plugin.
	 *
	 * @since 7.0.0
	 * @var array<string, array>
	 * @phpstan-var array<string, Connector>
	 */
	private array $registered_connectors = array();

	/**
	 * Registers a new connector.
	 *
	 * Validates the provided arguments and stores the connector in the registry.
	 * For connectors with `api_key` authentication, a `setting_name` is automatically
	 * generated using the pattern `connectors_ai_{$id}_api_key` (e.g., connector ID
	 * `openai` produces `connectors_ai_openai_api_key`). This setting name is used
	 * for the Settings API registration and REST API exposure.
	 *
	 * Registering a connector with an ID that is already registered will trigger a
	 * `_doing_it_wrong()` notice and return `null`. To override an existing connector,
	 * call `unregister()` first.
	 *
	 * @since 7.0.0
	 *
	 * @see WP_Connector_Registry::unregister()
	 *
	 * @param string $id   The unique connector identifier. Must match the pattern
	 *                     `/^[a-z0-9_]+$/` (lowercase alphanumeric and underscores only).
	 * @param array  $args {
	 *     An associative array of arguments for the connector.
	 *
	 *     @type string $name           Required. The connector's display name.
	 *     @type string $description    Optional. The connector's description. Default empty string.
	 *     @type string $logo_url       Optional. URL to the connector's logo image.
	 *     @type string $type           Required. The connector type. Currently, only 'ai_provider' is supported.
	 *     @type array  $authentication {
	 *         Required. Authentication configuration.
	 *
	 *         @type string $method          Required. The authentication method: 'api_key' or 'none'.
	 *         @type string $credentials_url Optional. URL where users can obtain API credentials.
	 *     }
	 *     @type array  $plugin         {
	 *         Optional. Plugin data for install/activate UI.
	 *
	 *         @type string $slug The WordPress.org plugin slug.
	 *     }
	 * }
	 * @return array|null The registered connector data on success, null on failure.
	 *
	 * @phpstan-param Connector $args
	 * @phpstan-return Connector|null
	 */
	public function register( string $id, array $args ): ?array {
		if ( ! preg_match( '/^[a-z0-9_]+$/', $id ) ) {
			_doing_it_wrong(
				__METHOD__,
				__(
					'Connector ID must contain only lowercase alphanumeric characters and underscores.'
				),
				'7.0.0'
			);
			return null;
		}

		if ( $this->is_registered( $id ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" is already registered.' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}

		// Validate required fields.
		if ( empty( $args['name'] ) || ! is_string( $args['name'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" requires a non-empty "name" string.' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}

		if ( empty( $args['type'] ) || ! is_string( $args['type'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" requires a non-empty "type" string.' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}

		if ( ! isset( $args['authentication'] ) || ! is_array( $args['authentication'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" requires an "authentication" array.' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}

		if ( empty( $args['authentication']['method'] ) || ! in_array( $args['authentication']['method'], array( 'api_key', 'none' ), true ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" authentication method must be "api_key" or "none".' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}

		$connector = array(
			'name'           => $args['name'],
			'description'    => isset( $args['description'] ) && is_string( $args['description'] ) ? $args['description'] : '',
			'type'           => $args['type'],
			'authentication' => array(
				'method' => $args['authentication']['method'],
			),
		);

		if ( ! empty( $args['logo_url'] ) && is_string( $args['logo_url'] ) ) {
			$connector['logo_url'] = $args['logo_url'];
		}

		if ( 'api_key' === $args['authentication']['method'] ) {
			if ( ! empty( $args['authentication']['credentials_url'] ) && is_string( $args['authentication']['credentials_url'] ) ) {
				$connector['authentication']['credentials_url'] = $args['authentication']['credentials_url'];
			}
			$connector['authentication']['setting_name'] = "connectors_ai_{$id}_api_key";
		}

		if ( ! empty( $args['plugin'] ) && is_array( $args['plugin'] ) ) {
			$connector['plugin'] = $args['plugin'];
		}

		$this->registered_connectors[ $id ] = $connector;
		return $connector;
	}

	/**
	 * Unregisters a connector.
	 *
	 * Returns the connector data on success, which can be modified and passed
	 * back to `register()` to override a connector's metadata.
	 *
	 * Triggers a `_doing_it_wrong()` notice if the connector is not registered.
	 * Use `is_registered()` to check first when the connector may not exist.
	 *
	 * @since 7.0.0
	 *
	 * @see WP_Connector_Registry::register()
	 * @see WP_Connector_Registry::is_registered()
	 *
	 * @param string $id The connector identifier.
	 * @return array|null The unregistered connector data on success, null on failure.
	 *
	 * @phpstan-return Connector|null
	 */
	public function unregister( string $id ): ?array {
		if ( ! $this->is_registered( $id ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" not found.' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}

		$unregistered = $this->registered_connectors[ $id ];
		unset( $this->registered_connectors[ $id ] );

		return $unregistered;
	}

	/**
	 * Retrieves the list of all registered connectors.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_connectors()` function.
	 *
	 * @since 7.0.0
	 *
	 * @see wp_get_connectors()
	 *
	 * @return array Connector settings keyed by connector ID.
	 *
	 * @phpstan-return array<string, Connector>
	 */
	public function get_all_registered(): array {
		return $this->registered_connectors;
	}

	/**
	 * Checks if a connector is registered.
	 *
	 * Do not use this method directly. Instead, use the `wp_is_connector_registered()` function.
	 *
	 * @since 7.0.0
	 *
	 * @see wp_is_connector_registered()
	 *
	 * @param string $id The connector identifier.
	 * @return bool True if the connector is registered, false otherwise.
	 */
	public function is_registered( string $id ): bool {
		return isset( $this->registered_connectors[ $id ] );
	}

	/**
	 * Retrieves a registered connector.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_connector()` function.
	 *
	 * Triggers a `_doing_it_wrong()` notice if the connector is not registered.
	 * Use `is_registered()` to check first when the connector may not exist.
	 *
	 * @since 7.0.0
	 *
	 * @see wp_get_connector()
	 *
	 * @param string $id The connector identifier.
	 * @return array|null The registered connector data, or null if it is not registered.
	 * @phpstan-return Connector|null
	 */
	public function get_registered( string $id ): ?array {
		if ( ! $this->is_registered( $id ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Connector ID. */
				sprintf( __( 'Connector "%s" not found.' ), esc_html( $id ) ),
				'7.0.0'
			);
			return null;
		}
		return $this->registered_connectors[ $id ];
	}

	/**
	 * Retrieves the main instance of the registry class.
	 *
	 * @since 7.0.0
	 *
	 * @return WP_Connector_Registry|null The main registry instance, or null if not yet initialized.
	 */
	public static function get_instance(): ?self {
		return self::$instance;
	}

	/**
	 * Sets the main instance of the registry class.
	 *
	 * Called by `_wp_connectors_init()` during the `init` action. Must not be
	 * called outside of that context.
	 *
	 * @since 7.0.0
	 * @access private
	 *
	 * @see _wp_connectors_init()
	 *
	 * @param WP_Connector_Registry $registry The registry instance.
	 */
	public static function set_instance( WP_Connector_Registry $registry ): void {
		if ( ! doing_action( 'init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The connector registry instance must be set during the <code>init</code> action.' ),
				'7.0.0'
			);
			return;
		}

		self::$instance = $registry;
	}
}
