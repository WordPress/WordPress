<?php
/**
 * Connectors API
 *
 * Defines WP_Connector_Registry class.
 *
 * @package WordPress
 * @subpackage Connectors
 * @since 7.0.0
 */

/**
 * Manages the registration and lookup of connectors.
 *
 * @since 7.0.0
 * @access private
 *
 * @phpstan-type Connector array{
 *     name: string,
 *     description: string,
 *     logo_url?: string|null,
 *     type: string,
 *     authentication: array{
 *         method: string,
 *         credentials_url?: string|null,
 *         setting_name?: string
 *     },
 *     plugin?: array{
 *         slug: string
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
	 * @since 7.0.0
	 *
	 * @param string $id   The unique connector identifier. Must contain only lowercase
	 *                     alphanumeric characters and underscores.
	 * @param array  $args {
	 *     An associative array of arguments for the connector.
	 *
	 *     @type string $name           Required. The connector's display name.
	 *     @type string $description    Optional. The connector's description. Default empty string.
	 *     @type string|null $logo_url  Optional. URL to the connector's logo image. Default null.
	 *     @type string $type           Required. The connector type. Currently, only 'ai_provider' is supported.
	 *     @type array  $authentication {
	 *         Required. Authentication configuration.
	 *
	 *         @type string      $method          Required. The authentication method: 'api_key' or 'none'.
	 *         @type string|null $credentials_url Optional. URL where users can obtain API credentials.
	 *     }
	 *     @type array  $plugin {
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
			$connector['authentication']['credentials_url'] = $args['authentication']['credentials_url'] ?? null;
			$connector['authentication']['setting_name']    = "connectors_ai_{$id}_api_key";
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
	 * @since 7.0.0
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
	 * @return array<string, array> The array of registered connectors keyed by connector ID.
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
	 * @since 7.0.0
	 * @access private
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
