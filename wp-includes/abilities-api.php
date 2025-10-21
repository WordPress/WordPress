<?php
/**
 * Abilities API
 *
 * Defines functions for managing abilities in WordPress.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Registers a new ability using Abilities API.
 *
 * Note: Should only be used on the {@see 'wp_abilities_api_init'} hook.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::register()
 *
 * @param string               $name The name of the ability. The name must be a string containing a namespace
 *                                   prefix, i.e. `my-plugin/my-ability`. It can only contain lowercase
 *                                   alphanumeric characters, dashes and the forward slash.
 * @param array<string, mixed> $args {
 *     An associative array of arguments for the ability.
 *
 *     @type string               $label               The human-readable label for the ability.
 *     @type string               $description         A detailed description of what the ability does.
 *     @type string               $category            The ability category slug this ability belongs to.
 *     @type callable             $execute_callback    A callback function to execute when the ability is invoked.
 *                                                     Receives optional mixed input and returns mixed result or WP_Error.
 *     @type callable             $permission_callback A callback function to check permissions before execution.
 *                                                     Receives optional mixed input and returns bool or WP_Error.
 *     @type array<string, mixed> $input_schema        Optional. JSON Schema definition for the ability's input.
 *     @type array<string, mixed> $output_schema       Optional. JSON Schema definition for the ability's output.
 *     @type array<string, mixed> $meta                  {
 *         Optional. Additional metadata for the ability.
 *
 *         @type array<string, null|bool> $annotations  Optional. Annotation metadata for the ability.
 *         @type bool                     $show_in_rest Optional. Whether to expose this ability in the REST API. Default false.
 *     }
 *     @type string               $ability_class       Optional. Custom class to instantiate instead of WP_Ability.
 * }
 * @return WP_Ability|null An instance of registered ability on success, null on failure.
 */
function wp_register_ability( string $name, array $args ): ?WP_Ability {
	if ( ! did_action( 'wp_abilities_api_init' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: abilities_api_init, 2: string value of the ability name. */
				esc_html__( 'Abilities must be registered on the %1$s action. The ability %2$s was not registered.' ),
				'<code>abilities_api_init</code>',
				'<code>' . esc_html( $name ) . '</code>'
			),
			'6.9.0'
		);
		return null;
	}

	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->register( $name, $args );
}

/**
 * Unregisters an ability from the Abilities API.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::unregister()
 *
 * @param string $name The name of the registered ability, with its namespace.
 * @return WP_Ability|null The unregistered ability instance on success, null on failure.
 */
function wp_unregister_ability( string $name ): ?WP_Ability {
	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->unregister( $name );
}

/**
 * Checks if an ability is registered.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::is_registered()
 *
 * @param string $name The name of the registered ability, with its namespace.
 * @return bool True if the ability is registered, false otherwise.
 */
function wp_has_ability( string $name ): bool {
	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return false;
	}

	return $registry->is_registered( $name );
}

/**
 * Retrieves a registered ability using Abilities API.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::get_registered()
 *
 * @param string $name The name of the registered ability, with its namespace.
 * @return WP_Ability|null The registered ability instance, or null if it is not registered.
 */
function wp_get_ability( string $name ): ?WP_Ability {
	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->get_registered( $name );
}

/**
 * Retrieves all registered abilities using Abilities API.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::get_all_registered()
 *
 * @return WP_Ability[] The array of registered abilities.
 */
function wp_get_abilities(): array {
	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return array();
	}

	return $registry->get_all_registered();
}

/**
 * Registers a new ability category.
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::register()
 *
 * @param string               $slug The unique slug for the ability category. Must contain only lowercase
 *                                   alphanumeric characters and dashes.
 * @param array<string, mixed> $args {
 *     An associative array of arguments for the ability category.
 *
 *     @type string               $label       The human-readable label for the ability category.
 *     @type string               $description A description of the ability category.
 *     @type array<string, mixed> $meta        Optional. Additional metadata for the ability category.
 * }
 * @return WP_Ability_Category|null The registered ability category instance on success, null on failure.
 */
function wp_register_ability_category( string $slug, array $args ): ?WP_Ability_Category {
	if ( ! did_action( 'wp_abilities_api_categories_init' ) ) {
		_doing_it_wrong(
			__METHOD__,
			sprintf(
				/* translators: 1: abilities_api_categories_init, 2: ability category slug. */
				__( 'Ability categories must be registered on the %1$s action. The ability category %2$s was not registered.' ),
				'<code>wp_abilities_api_categories_init</code>',
				'<code>' . esc_html( $slug ) . '</code>'
			),
			'6.9.0'
		);
		return null;
	}

	$registry = WP_Ability_Categories_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->register( $slug, $args );
}

/**
 * Unregisters an ability category.
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::unregister()
 *
 * @param string $slug The slug of the registered ability category.
 * @return WP_Ability_Category|null The unregistered ability category instance on success, null on failure.
 */
function wp_unregister_ability_category( string $slug ): ?WP_Ability_Category {
	$registry = WP_Ability_Categories_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->unregister( $slug );
}

/**
 * Checks if an ability category is registered.
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::is_registered()
 *
 * @param string $slug The slug of the ability category.
 * @return bool True if the ability category is registered, false otherwise.
 */
function wp_has_ability_category( string $slug ): bool {
	$registry = WP_Ability_Categories_Registry::get_instance();
	if ( null === $registry ) {
		return false;
	}

	return $registry->is_registered( $slug );
}

/**
 * Retrieves a registered ability category.
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::get_registered()
 *
 * @param string $slug The slug of the registered ability category.
 * @return WP_Ability_Category|null The registered ability category instance, or null if it is not registered.
 */
function wp_get_ability_category( string $slug ): ?WP_Ability_Category {
	$registry = WP_Ability_Categories_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->get_registered( $slug );
}

/**
 * Retrieves all registered ability categories.
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::get_all_registered()
 *
 * @return WP_Ability_Category[] The array of registered ability categories.
 */
function wp_get_ability_categories(): array {
	$registry = WP_Ability_Categories_Registry::get_instance();
	if ( null === $registry ) {
		return array();
	}

	return $registry->get_all_registered();
}
