<?php
/**
 * Abilities API
 *
 * Defines WP_Abilities_Registry class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Manages the registration and lookup of abilities.
 *
 * @since 6.9.0
 * @access private
 */
final class WP_Abilities_Registry {
	/**
	 * The singleton instance of the registry.
	 *
	 * @since 6.9.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Holds the registered abilities.
	 *
	 * @since 6.9.0
	 * @var WP_Ability[]
	 */
	private $registered_abilities = array();

	/**
	 * Registers a new ability.
	 *
	 * Do not use this method directly. Instead, use the `wp_register_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_register_ability()
	 *
	 * @param string               $name The name of the ability. The name must be a string containing a namespace
	 *                                   prefix, i.e. `my-plugin/my-ability`. It can only contain lowercase
	 *                                   alphanumeric characters, dashes and the forward slash.
	 * @param array<string, mixed> $args {
	 *     An associative array of arguments for the ability.
	 *
	 *     @type string               $label                 The human-readable label for the ability.
	 *     @type string               $description           A detailed description of what the ability does.
	 *     @type string               $category              The ability category slug this ability belongs to.
	 *     @type callable             $execute_callback      A callback function to execute when the ability is invoked.
	 *                                                       Receives optional mixed input and returns mixed result or WP_Error.
	 *     @type callable             $permission_callback   A callback function to check permissions before execution.
	 *                                                       Receives optional mixed input and returns bool or WP_Error.
	 *     @type array<string, mixed> $input_schema          Optional. JSON Schema definition for the ability's input.
	 *     @type array<string, mixed> $output_schema         Optional. JSON Schema definition for the ability's output.
	 *     @type array<string, mixed> $meta                  {
	 *         Optional. Additional metadata for the ability.
	 *
	 *         @type array<string, null|bool> $annotations  Optional. Annotation metadata for the ability.
	 *         @type bool                     $show_in_rest Optional. Whether to expose this ability in the REST API. Default false.
	 *     }
	 *     @type string               $ability_class         Optional. Custom class to instantiate instead of WP_Ability.
	 * }
	 * @return WP_Ability|null The registered ability instance on success, null on failure.
	 */
	public function register( string $name, array $args ): ?WP_Ability {
		if ( ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__(
					'Ability name must be a string containing a namespace prefix, i.e. "my-plugin/my-ability". It can only contain lowercase alphanumeric characters, dashes and the forward slash.'
				),
				'6.9.0'
			);
			return null;
		}

		if ( $this->is_registered( $name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability name. */
				sprintf( __( 'Ability "%s" is already registered.' ), esc_html( $name ) ),
				'6.9.0'
			);
			return null;
		}

		/**
		 * Filters the ability arguments before they are validated and used to instantiate the ability.
		 *
		 * @since 6.9.0
		 *
		 * @param array<string, mixed> $args {
		 *     An associative array of arguments for the ability.
		 *
		 *     @type string               $label                 The human-readable label for the ability.
		 *     @type string               $description           A detailed description of what the ability does.
		 *     @type string               $category              The ability category slug this ability belongs to.
		 *     @type callable             $execute_callback      A callback function to execute when the ability is invoked.
		 *                                                       Receives optional mixed input and returns mixed result or WP_Error.
		 *     @type callable             $permission_callback   A callback function to check permissions before execution.
		 *                                                       Receives optional mixed input and returns bool or WP_Error.
		 *     @type array<string, mixed> $input_schema          Optional. JSON Schema definition for the ability's input.
		 *     @type array<string, mixed> $output_schema         Optional. JSON Schema definition for the ability's output.
		 *     @type array<string, mixed> $meta                  {
		 *         Optional. Additional metadata for the ability.
		 *
		 *         @type array<string, bool|string> $annotations  Optional. Annotation metadata for the ability.
		 *         @type bool                       $show_in_rest Optional. Whether to expose this ability in the REST API. Default false.
		 *     }
		 *     @type string               $ability_class         Optional. Custom class to instantiate instead of WP_Ability.
		 * }
		 * @param string               $name The name of the ability, with its namespace.
		 */
		$args = apply_filters( 'wp_register_ability_args', $args, $name );

		// Validate ability category exists if provided (will be validated as required in WP_Ability).
		if ( isset( $args['category'] ) ) {
			if ( ! wp_has_ability_category( $args['category'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %1$s: ability category slug, %2$s: ability name */
						__( 'Ability category "%1$s" is not registered. Please register the ability category before assigning it to ability "%2$s".' ),
						esc_html( $args['category'] ),
						esc_html( $name )
					),
					'6.9.0'
				);
				return null;
			}
		}

		// The class is only used to instantiate the ability, and is not a property of the ability itself.
		if ( isset( $args['ability_class'] ) && ! is_a( $args['ability_class'], WP_Ability::class, true ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The ability args should provide a valid `ability_class` that extends WP_Ability.' ),
				'6.9.0'
			);
			return null;
		}

		/** @var class-string<WP_Ability> */
		$ability_class = $args['ability_class'] ?? WP_Ability::class;
		unset( $args['ability_class'] );

		try {
			// WP_Ability::prepare_properties() will throw an exception if the properties are invalid.
			$ability = new $ability_class( $name, $args );
		} catch ( InvalidArgumentException $e ) {
			_doing_it_wrong(
				__METHOD__,
				$e->getMessage(),
				'6.9.0'
			);
			return null;
		}

		$this->registered_abilities[ $name ] = $ability;
		return $ability;
	}

	/**
	 * Unregisters an ability.
	 *
	 * Do not use this method directly. Instead, use the `wp_unregister_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_unregister_ability()
	 *
	 * @param string $name The name of the registered ability, with its namespace.
	 * @return WP_Ability|null The unregistered ability instance on success, null on failure.
	 */
	public function unregister( string $name ): ?WP_Ability {
		if ( ! $this->is_registered( $name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability name. */
				sprintf( __( 'Ability "%s" not found.' ), esc_html( $name ) ),
				'6.9.0'
			);
			return null;
		}

		$unregistered_ability = $this->registered_abilities[ $name ];
		unset( $this->registered_abilities[ $name ] );

		return $unregistered_ability;
	}

	/**
	 * Retrieves the list of all registered abilities.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_abilities()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_get_abilities()
	 *
	 * @return WP_Ability[] The array of registered abilities.
	 */
	public function get_all_registered(): array {
		return $this->registered_abilities;
	}

	/**
	 * Checks if an ability is registered.
	 *
	 * Do not use this method directly. Instead, use the `wp_has_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_has_ability()
	 *
	 * @param string $name The name of the registered ability, with its namespace.
	 * @return bool True if the ability is registered, false otherwise.
	 */
	public function is_registered( string $name ): bool {
		return isset( $this->registered_abilities[ $name ] );
	}

	/**
	 * Retrieves a registered ability.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_get_ability()
	 *
	 * @param string $name The name of the registered ability, with its namespace.
	 * @return WP_Ability|null The registered ability instance, or null if it is not registered.
	 */
	public function get_registered( string $name ): ?WP_Ability {
		if ( ! $this->is_registered( $name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability name. */
				sprintf( __( 'Ability "%s" not found.' ), esc_html( $name ) ),
				'6.9.0'
			);
			return null;
		}
		return $this->registered_abilities[ $name ];
	}

	/**
	 * Utility method to retrieve the main instance of the registry class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 6.9.0
	 *
	 * @return WP_Abilities_Registry|null The main registry instance, or null when `init` action has not fired.
	 */
	public static function get_instance(): ?self {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					// translators: %s: init action.
					__( 'Ability API should not be initialized before the %s action has fired.' ),
					'<code>init</code>'
				),
				'6.9.0'
			);
			return null;
		}

		if ( null === self::$instance ) {
			self::$instance = new self();

			// Ensure ability category registry is initialized first to allow categories to be registered
			// before abilities that depend on them.
			WP_Ability_Categories_Registry::get_instance();

			/**
			 * Fires when preparing abilities registry.
			 *
			 * Abilities should be created and register their hooks on this action rather
			 * than another action to ensure they're only loaded when needed.
			 *
			 * @since 6.9.0
			 *
			 * @param WP_Abilities_Registry $instance Abilities registry object.
			 */
			do_action( 'wp_abilities_api_init', self::$instance );
		}

		return self::$instance;
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the registry object is unserialized.
	 *                        This is a security hardening measure to prevent unserialization of the registry.
	 */
	public function __wakeup(): void {
		throw new LogicException( __CLASS__ . ' should never be unserialized.' );
	}

	/**
	 * Sleep magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the registry object is serialized.
	 *                        This is a security hardening measure to prevent serialization of the registry.
	 */
	public function __sleep(): array {
		throw new LogicException( __CLASS__ . ' should never be serialized.' );
	}
}
