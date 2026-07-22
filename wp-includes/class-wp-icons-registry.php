<?php
/**
 * Icons API: WP_Icons_Registry class
 *
 * @package WordPress
 * @since 7.0.0
 */

/**
 * Core class used for interacting with registered icons.
 *
 * @since 7.0.0
 */
class WP_Icons_Registry {
	/**
	 * Registered icons array.
	 *
	 * @since 7.0.0
	 * @var array[]
	 */
	protected $registered_icons = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 7.0.0
	 * @var WP_Icons_Registry|null
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * WP_Icons_Registry is a singleton class, so keep this protected.
	 *
	 * Icons are populated via `_wp_register_default_icons()` during the
	 * `init` action. Third-party icons can be registered via
	 * {@see wp_register_icon()} once their collection is registered.
	 *
	 * @since 7.0.0
	 */
	protected function __construct() {}

	/**
	 * Registers an icon.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 The icon name must be namespaced in the form "collection/icon-name".
	 *
	 * @param string $icon_name       Namespaced icon name in the form "collection/icon-name"
	 *                                (e.g. "core/arrow-left").
	 * @param array  $icon_properties {
	 *     List of properties for the icon.
	 *
	 *     @type string $label     Required. A human-readable label for the icon.
	 *     @type string $content   Optional. SVG markup for the icon.
	 *                             If not provided, the content will be retrieved from the `file_path` if set.
	 *                             If both `content` and `file_path` are not set, the icon will not be registered.
	 *     @type string $file_path Optional. The full path to the file containing the icon content.
	 * }
	 * @return bool True if the icon was registered with success and false otherwise.
	 */
	public function register( $icon_name, $icon_properties ) {
		if ( ! isset( $icon_name ) || ! is_string( $icon_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon name must be a string.' ),
				'7.0.0'
			);
			return false;
		}

		// Require a namespaced name in the form "collection/icon-name".
		if ( ! str_contains( $icon_name, '/' ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon name must be namespaced in the form "collection/icon-name".' ),
				'7.1.0'
			);
			return false;
		}

		// Split the namespaced name into a collection slug and an unqualified icon name.
		list( $collection, $unqualified_name ) = explode( '/', $icon_name, 2 );

		if ( preg_match( '/[A-Z]/', $unqualified_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon names must not contain uppercase characters.' ),
				'7.1.0'
			);
			return false;
		}

		if ( ! preg_match( '/^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$/', $unqualified_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon names must start and end with a lowercase letter or digit and contain only lowercase letters, digits, hyphens, and underscores.' ),
				'7.1.0'
			);
			return false;
		}

		$allowed_keys = array_fill_keys( array( 'label', 'content', 'file_path' ), 1 );
		foreach ( array_keys( $icon_properties ) as $key ) {
			if ( ! array_key_exists( $key, $allowed_keys ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: The name of a user-provided key. */
						__( 'Invalid icon property: "%s".' ),
						$key
					),
					'7.0.0'
				);
				return false;
			}
		}

		if ( ! WP_Icon_Collections_Registry::get_instance()->is_registered( $collection ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: Icon collection slug. */
					__( 'Icon collection "%s" is not registered.' ),
					$collection
				),
				'7.1.0'
			);
			return false;
		}

		if ( ! isset( $icon_properties['label'] ) || ! is_string( $icon_properties['label'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon label must be a string.' ),
				'7.0.0'
			);
			return false;
		}

		if (
			( ! isset( $icon_properties['content'] ) && ! isset( $icon_properties['file_path'] ) ) ||
			( isset( $icon_properties['content'] ) && isset( $icon_properties['file_path'] ) )
		) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icons must provide either `content` or `file_path`.' ),
				'7.0.0'
			);
			return false;
		}

		if ( isset( $icon_properties['content'] ) ) {
			if ( ! is_string( $icon_properties['content'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'Icon content must be a string.' ),
					'7.0.0'
				);
				return false;
			}

			$sanitized_icon_content = $this->sanitize_icon_content( $icon_properties['content'] );
			if ( empty( $sanitized_icon_content ) ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'Icon content does not contain valid SVG markup.' ),
					'7.0.0'
				);
				return false;
			}

			$icon_properties['content'] = $sanitized_icon_content;
		}

		$qualified_name = $collection . '/' . $unqualified_name;

		if ( $this->is_registered( $qualified_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Icon is already registered.' ),
				'7.1.0'
			);
			return false;
		}

		$icon = array_merge(
			$icon_properties,
			array(
				'name'       => $qualified_name,
				'collection' => $collection,
			)
		);

		$this->registered_icons[ $qualified_name ] = $icon;

		return true;
	}

	/**
	 * Unregisters an icon.
	 *
	 * @since 7.1.0
	 *
	 * @param string $icon_name Namespaced icon name in the form "collection/icon-name"
	 *                          (e.g. "core/arrow-left").
	 * @return bool True if the icon was unregistered successfully, false otherwise.
	 */
	public function unregister( $icon_name ) {
		if ( ! $this->is_registered( $icon_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: Icon name. */
					__( 'Icon "%s" is not registered.' ),
					$icon_name
				),
				'7.1.0'
			);
			return false;
		}

		unset( $this->registered_icons[ $icon_name ] );
		return true;
	}

	/**
	 * Sanitizes the icon SVG content.
	 *
	 * Logic borrowed from twentytwenty.
	 * @see twentytwenty_get_theme_svg
	 *
	 * @since 7.0.0
	 *
	 * @param string $icon_content The icon SVG content to sanitize.
	 * @return string The sanitized icon SVG content.
	 */
	protected function sanitize_icon_content( $icon_content ) {
		$allowed_tags = array(
			'svg'     => array(
				'class'       => true,
				'xmlns'       => true,
				'width'       => true,
				'height'      => true,
				'viewbox'     => true,
				'aria-hidden' => true,
				'role'        => true,
				'focusable'   => true,
			),
			'path'    => array(
				'fill'      => true,
				'fill-rule' => true,
				'd'         => true,
				'transform' => true,
			),
			'polygon' => array(
				'fill'      => true,
				'fill-rule' => true,
				'points'    => true,
				'transform' => true,
				'focusable' => true,
			),
		);
		return wp_kses( $icon_content, $allowed_tags );
	}

	/**
	 * Retrieves the content of a registered icon.
	 *
	 * @since 7.0.0
	 *
	 * @param string $icon_name Icon name including namespace.
	 * @return string|null The content of the icon, if found.
	 */
	protected function get_content( $icon_name ) {
		if ( ! isset( $this->registered_icons[ $icon_name ]['content'] ) ) {
			$file_path  = $this->registered_icons[ $icon_name ]['file_path'] ?? '';
			$is_stringy = is_string( $file_path ) || ( is_object( $file_path ) && method_exists( $file_path, '__toString' ) );
			$icon_path  = $is_stringy ? realpath( (string) $file_path ) : false;

			if (
				! is_string( $icon_path ) ||
				! str_ends_with( $icon_path, '.svg' ) ||
				! is_file( $icon_path ) ||
				! is_readable( $icon_path )
			) {
				wp_trigger_error(
					__METHOD__,
					__( 'Icon file is missing or unreadable.' )
				);
				return null;
			}

			$content = $this->sanitize_icon_content( file_get_contents( $icon_path ) );

			if ( empty( $content ) ) {
				wp_trigger_error(
					__METHOD__,
					__( 'Icon content does not contain valid SVG markup.' )
				);
				return null;
			}

			$this->registered_icons[ $icon_name ]['content'] = $content;
		}
		return $this->registered_icons[ $icon_name ]['content'];
	}

	/**
	 * Retrieves an array containing the properties of a registered icon.
	 *
	 * @since 7.0.0
	 *
	 * @param string $icon_name Icon name including namespace.
	 * @return array|null Registered icon properties or `null` if the icon is not registered.
	 */
	public function get_registered_icon( $icon_name ) {
		if ( ! $this->is_registered( $icon_name ) ) {
			return null;
		}

		$icon            = $this->registered_icons[ $icon_name ];
		$icon['content'] = $icon['content'] ?? $this->get_content( $icon_name );

		return $icon;
	}

	/**
	 * Retrieves all registered icons.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 Search also matches icon labels.
	 *
	 * @param string $search Optional. Search term by which to filter the icons.
	 * @return array[] Array of arrays containing the registered icon properties.
	 */
	public function get_registered_icons( $search = '' ) {
		$icons = array();

		foreach ( $this->registered_icons as $icon ) {
			if ( ! empty( $search )
				&& false === stripos( $icon['name'], $search )
				&& false === stripos( $icon['label'] ?? '', $search )
			) {
				continue;
			}

			$icon['content'] = $icon['content'] ?? $this->get_content( $icon['name'] );
			$icons[]         = $icon;
		}

		return $icons;
	}

	/**
	 * Checks if an icon is registered.
	 *
	 * @since 7.0.0
	 *
	 * @param string $icon_name Icon name including namespace.
	 * @return bool True if the icon is registered, false otherwise.
	 */
	public function is_registered( $icon_name ) {
		return isset( $this->registered_icons[ $icon_name ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 7.0.0
	 *
	 * @return WP_Icons_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
