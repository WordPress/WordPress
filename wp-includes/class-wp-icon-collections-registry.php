<?php
/**
 * Icons API: WP_Icon_Collections_Registry class
 *
 * @package WordPress
 * @since 7.1.0
 */

/**
 * Core class used for interacting with registered icon collections.
 *
 * Icons are always associated with a single collection. Collections act as
 * a namespace for icons and allow grouping icons coming from different
 * sources (e.g. core, Font Awesome, Google Material Icons).
 *
 * @since 7.1.0
 */
class WP_Icon_Collections_Registry {

	/**
	 * Registered icon collections array.
	 *
	 * @since 7.1.0
	 * @var array[]
	 */
	protected $registered_collections = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 7.1.0
	 * @var WP_Icon_Collections_Registry|null
	 */
	protected static $instance = null;

	/**
	 * Registers an icon collection.
	 *
	 * @since 7.1.0
	 *
	 * @param string $collection_slug       Icon collection slug.
	 * @param array  $collection_properties {
	 *     List of properties for the icon collection.
	 *
	 *     @type string $label       Required. A human-readable label for the icon collection.
	 *     @type string $description Optional. A human-readable description for the icon collection.
	 * }
	 * @return bool True if the collection was registered successfully, false otherwise.
	 */
	public function register( $collection_slug, $collection_properties ) {
		if ( ! isset( $collection_slug ) || ! is_string( $collection_slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon collection slug must be a string.' ),
				'7.1.0'
			);
			return false;
		}

		if ( ! preg_match( '/^[a-z0-9]([a-z0-9_-]*[a-z0-9])?$/', $collection_slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon collection slug must start and end with a lowercase letter or digit and contain only lowercase letters, digits, hyphens, and underscores.' ),
				'7.1.0'
			);
			return false;
		}

		if ( $this->is_registered( $collection_slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon collection is already registered.' ),
				'7.1.0'
			);
			return false;
		}

		if ( ! is_array( $collection_properties ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon collection properties must be an array.' ),
				'7.1.0'
			);
			return false;
		}

		$allowed_keys = array_fill_keys( array( 'label', 'description' ), 1 );
		foreach ( array_keys( $collection_properties ) as $key ) {
			if ( ! array_key_exists( $key, $allowed_keys ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: The name of a user-provided key. */
						__( 'Invalid icon collection property: "%s".' ),
						$key
					),
					'7.1.0'
				);
				return false;
			}
		}

		if ( ! isset( $collection_properties['label'] ) || ! is_string( $collection_properties['label'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon collection label must be a string.' ),
				'7.1.0'
			);
			return false;
		}

		if ( isset( $collection_properties['description'] ) && ! is_string( $collection_properties['description'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon collection description must be a string.' ),
				'7.1.0'
			);
			return false;
		}

		$defaults = array(
			'description' => '',
		);

		$collection = array_merge(
			$defaults,
			$collection_properties,
			array( 'slug' => $collection_slug )
		);

		$this->registered_collections[ $collection_slug ] = $collection;

		return true;
	}

	/**
	 * Unregisters an icon collection.
	 *
	 * Any icons registered under the given collection are also unregistered.
	 *
	 * @since 7.1.0
	 *
	 * @param string $collection_slug Icon collection slug.
	 * @return bool True if the collection was unregistered successfully, false otherwise.
	 */
	public function unregister( $collection_slug ) {
		if ( ! $this->is_registered( $collection_slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: Icon collection slug. */
					__( 'Icon collection "%s" not found.' ),
					$collection_slug
				),
				'7.1.0'
			);
			return false;
		}

		$icons_registry = WP_Icons_Registry::get_instance();
		foreach ( $icons_registry->get_registered_icons() as $icon ) {
			if ( isset( $icon['collection'] ) && $icon['collection'] === $collection_slug ) {
				$icons_registry->unregister( $icon['name'] );
			}
		}

		unset( $this->registered_collections[ $collection_slug ] );

		return true;
	}

	/**
	 * Retrieves an array containing the properties of a registered icon collection.
	 *
	 * @since 7.1.0
	 *
	 * @param string $collection_slug Icon collection slug.
	 * @return array|null Registered collection properties, or `null` if the collection is not registered.
	 */
	public function get_registered( $collection_slug ) {
		if ( ! $this->is_registered( $collection_slug ) ) {
			return null;
		}

		return $this->registered_collections[ $collection_slug ];
	}

	/**
	 * Retrieves all registered icon collections.
	 *
	 * @since 7.1.0
	 *
	 * @return array[] Array of arrays containing the registered icon collections properties.
	 */
	public function get_all_registered() {
		return array_values( $this->registered_collections );
	}

	/**
	 * Checks if an icon collection is registered.
	 *
	 * @since 7.1.0
	 *
	 * @param string|null $collection_slug Icon collection slug.
	 * @return bool True if the icon collection is registered, false otherwise.
	 */
	public function is_registered( $collection_slug ) {
		return isset( $collection_slug, $this->registered_collections[ $collection_slug ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 7.1.0
	 *
	 * @return WP_Icon_Collections_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
