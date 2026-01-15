<?php
/**
 * Abilities API: core functions for registering and managing abilities.
 *
 * The Abilities API provides a unified, extensible framework for registering
 * and executing discrete capabilities within WordPress. An "ability" is a
 * self-contained unit of functionality with defined inputs, outputs, permissions,
 * and execution logic.
 *
 * ## Overview
 *
 * The Abilities API enables developers to:
 *
 *  - Register custom abilities with standardized interfaces.
 *  - Define permission checks and execution callbacks.
 *  - Organize abilities into logical categories.
 *  - Validate inputs and outputs using JSON Schema.
 *  - Expose abilities through the REST API.
 *
 * ## Working with Abilities
 *
 * Abilities must be registered on the `wp_abilities_api_init` action hook.
 * Attempting to register an ability outside of this hook will fail and
 * trigger a `_doing_it_wrong()` notice.

 * Example:
 *
 *     function my_plugin_register_abilities(): void {
 *         wp_register_ability(
 *             'my-plugin/export-users',
 *             array(
 *                 'label'               => __( 'Export Users', 'my-plugin' ),
 *                 'description'         => __( 'Exports user data to CSV format.', 'my-plugin' ),
 *                 'category'            => 'data-export',
 *                 'execute_callback'    => 'my_plugin_export_users',
 *                 'permission_callback' => function(): bool {
 *                     return current_user_can( 'export' );
 *                 },
 *                 'input_schema'        => array(
 *                     'type'        => 'string',
 *                     'enum'        => array( 'subscriber', 'contributor', 'author', 'editor', 'administrator' ),
 *                     'description' => __( 'Limits the export to users with this role.', 'my-plugin' ),
 *                     'required'    => false,
 *                 ),
 *                 'output_schema'       => array(
 *                     'type'        => 'string',
 *                     'description' => __( 'User data in CSV format.', 'my-plugin' ),
 *                     'required'    => true,
 *                 ),
 *                 'meta'                => array(
 *                     'show_in_rest' => true,
 *                 ),
 *             )
 *         );
 *     }
 *     add_action( 'wp_abilities_api_init', 'my_plugin_register_abilities' );
 *
 * Once registered, abilities can be checked, retrieved, and managed:
 *
 *     // Checks if an ability is registered, and prints its label.
 *     if ( wp_has_ability( 'my-plugin/export-users' ) ) {
 *         $ability = wp_get_ability( 'my-plugin/export-users' );
 *
 *         echo $ability->get_label();
 *     }
 *
 *     // Gets all registered abilities.
 *     $all_abilities = wp_get_abilities();
 *
 *     // Unregisters when no longer needed.
 *     wp_unregister_ability( 'my-plugin/export-users' );
 *
 * ## Best Practices
 *
 *  - Always register abilities on the `wp_abilities_api_init` hook.
 *  - Use namespaced ability names to prevent conflicts.
 *  - Implement robust permission checks in permission callbacks.
 *  - Provide an `input_schema` to ensure data integrity and document expected inputs.
 *  - Define an `output_schema` to describe return values and validate responses.
 *  - Return `WP_Error` objects for failures rather than throwing exceptions.
 *  - Use internationalization functions for all user-facing strings.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Registers a new ability using the Abilities API. It requires three steps:
 *
 *  1. Hook into the `wp_abilities_api_init` action.
 *  2. Call `wp_register_ability()` with a namespaced name and configuration.
 *  3. Provide execute and permission callbacks.
 *
 * Example:
 *
 *     function my_plugin_register_abilities(): void {
 *         wp_register_ability(
 *             'my-plugin/analyze-text',
 *             array(
 *                 'label'               => __( 'Analyze Text', 'my-plugin' ),
 *                 'description'         => __( 'Performs sentiment analysis on provided text.', 'my-plugin' ),
 *                 'category'            => 'text-processing',
 *                 'input_schema'        => array(
 *                     'type'        => 'string',
 *                     'description' => __( 'The text to be analyzed.', 'my-plugin' ),
 *                     'minLength'   => 10,
 *                     'required'    => true,
 *                 ),
 *                 'output_schema'       => array(
 *                     'type'        => 'string',
 *                     'enum'        => array( 'positive', 'negative', 'neutral' ),
 *                     'description' => __( 'The sentiment result: positive, negative, or neutral.', 'my-plugin' ),
 *                     'required'    => true,
 *                 ),
 *                 'execute_callback'    => 'my_plugin_analyze_text',
 *                 'permission_callback' => 'my_plugin_can_analyze_text',
 *                 'meta'                => array(
 *                     'annotations'   => array(
 *                         'readonly' => true,
 *                     ),
 *                     'show_in_rest' => true,
 *                 ),
 *             )
 *         );
 *     }
 *     add_action( 'wp_abilities_api_init', 'my_plugin_register_abilities' );
 *
 * ### Naming Conventions
 *
 * Ability names must follow these rules:
 *
 *  - Include a namespace prefix (e.g., `my-plugin/my-ability`).
 *  - Use only lowercase alphanumeric characters, dashes, and forward slashes.
 *  - Use descriptive, action-oriented names (e.g., `process-payment`, `generate-report`).
 *
 * ### Categories
 *
 * Abilities must be organized into categories. Ability categories provide better
 * discoverability and must be registered before the abilities that reference them:
 *
 *     function my_plugin_register_categories(): void {
 *         wp_register_ability_category(
 *             'text-processing',
 *             array(
 *                 'label'       => __( 'Text Processing', 'my-plugin' ),
 *                 'description' => __( 'Abilities for analyzing and transforming text.', 'my-plugin' ),
 *             )
 *         );
 *     }
 *     add_action( 'wp_abilities_api_categories_init', 'my_plugin_register_categories' );
 *
 * ### Input and Output Schemas
 *
 * Schemas define the expected structure, type, and constraints for ability inputs
 * and outputs using JSON Schema syntax. They serve two critical purposes: automatic
 * validation of data passed to and returned from abilities, and self-documenting
 * API contracts for developers.
 *
 * WordPress implements a validator based on a subset of the JSON Schema Version 4
 * specification (https://json-schema.org/specification-links.html#draft-4).
 * For details on supported JSON Schema properties and syntax, see the
 * related WordPress REST API Schema documentation:
 * https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#json-schema-basics
 *
 * Defining schemas is mandatory when there is a value to pass or return.
 * They ensure data integrity, improve developer experience, and enable
 * better documentation:
 *
 *     'input_schema' => array(
 *         'type'        => 'string',
 *         'description' => __( 'The text to be analyzed.', 'my-plugin' ),
 *         'minLength'   => 10,
 *         'required'    => true,
 *     ),
 *     'output_schema'       => array(
 *         'type'        => 'string',
 *         'enum'        => array( 'positive', 'negative', 'neutral' ),
 *         'description' => __( 'The sentiment result: positive, negative, or neutral.', 'my-plugin' ),
 *         'required'    => true,
 *     ),
 *
 * ### Callbacks
 *
 * #### Execute Callback
 *
 * The execute callback performs the ability's core functionality. It receives
 * optional input data and returns either a result or `WP_Error` on failure.
 *
 *     function my_plugin_analyze_text( string $input ): string|WP_Error {
 *         $score = My_Plugin::perform_sentiment_analysis( $input );
 *         if ( is_wp_error( $score ) ) {
 *             return $score;
 *         }
 *         return My_Plugin::interpret_sentiment_score( $score );
 *     }
 *
 * #### Permission Callback
 *
 * The permission callback determines whether the ability can be executed.
 * It receives the same input as the execute callback and must return a
 * boolean or `WP_Error`. Common use cases include checking user capabilities,
 * validating API keys, or verifying system state:
 *
 *     function my_plugin_can_analyze_text( string $input ): bool|WP_Error {
 *         return current_user_can( 'edit_posts' );
 *     }
 *
 * ### REST API Integration
 *
 * Abilities can be exposed through the REST API by setting `show_in_rest`
 * to `true` in the meta configuration:
 *
 *     'meta' => array(
 *         'show_in_rest' => true,
 *     ),
 *
 * This allows abilities to be invoked via HTTP requests to the WordPress REST API.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::register()
 * @see wp_register_ability_category()
 * @see wp_unregister_ability()
 *
 * @param string               $name The name of the ability. Must be a namespaced string containing
 *                                   a prefix, e.g., `my-plugin/my-ability`. Can only contain lowercase
 *                                   alphanumeric characters, dashes, and forward slashes.
 * @param array<string, mixed> $args {
 *     An associative array of arguments for configuring the ability.
 *
 *     @type string               $label               Required. The human-readable label for the ability.
 *     @type string               $description         Required. A detailed description of what the ability does
 *                                                     and when it should be used.
 *     @type string               $category            Required. The ability category slug this ability belongs to.
 *                                                     The ability category must be registered via `wp_register_ability_category()`
 *                                                     before registering the ability.
 *     @type callable             $execute_callback    Required. A callback function to execute when the ability is invoked.
 *                                                     Receives optional mixed input data and must return either a result
 *                                                     value (any type) or a `WP_Error` object on failure.
 *     @type callable             $permission_callback Required. A callback function to check permissions before execution.
 *                                                     Receives optional mixed input data (same as `execute_callback`) and
 *                                                     must return `true`/`false` for simple checks, or `WP_Error` for
 *                                                     detailed error responses.
 *     @type array<string, mixed> $input_schema        Optional. JSON Schema definition for validating the ability's input.
 *                                                     Must be a valid JSON Schema object defining the structure and
 *                                                     constraints for input data. Used for automatic validation and
 *                                                     API documentation.
 *     @type array<string, mixed> $output_schema       Optional. JSON Schema definition for the ability's output.
 *                                                     Describes the structure of successful return values from
 *                                                     `execute_callback`. Used for documentation and validation.
 *     @type array<string, mixed> $meta                {
 *         Optional. Additional metadata for the ability.
 *
 *         @type array<string, bool|null> $annotations  {
 *             Optional. Semantic annotations describing the ability's behavioral characteristics.
 *             These annotations are hints for tooling and documentation.
 *
 *             @type bool|null $readonly    Optional. If true, the ability does not modify its environment.
 *             @type bool|null $destructive Optional. If true, the ability may perform destructive updates to its environment.
 *                                          If false, the ability performs only additive updates.
 *             @type bool|null $idempotent  Optional. If true, calling the ability repeatedly with the same arguments
 *                                          will have no additional effect on its environment.
 *         }
 *         @type bool                     $show_in_rest Optional. Whether to expose this ability in the REST API.
 *                                                      When true, the ability can be invoked via HTTP requests.
 *                                                      Default false.
 *     }
 *     @type string               $ability_class       Optional. Fully-qualified custom class name to instantiate
 *                                                     instead of the default `WP_Ability` class. The custom class
 *                                                     must extend `WP_Ability`. Useful for advanced customization
 *                                                     of ability behavior.
 * }
 * @return WP_Ability|null The registered ability instance on success, `null` on failure.
 */
function wp_register_ability( string $name, array $args ): ?WP_Ability {
	if ( ! doing_action( 'wp_abilities_api_init' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: wp_abilities_api_init, 2: string value of the ability name. */
				__( 'Abilities must be registered on the %1$s action. The ability %2$s was not registered.' ),
				'<code>wp_abilities_api_init</code>',
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
 * Removes a previously registered ability from the global registry. Use this to
 * disable abilities provided by other plugins or when an ability is no longer needed.
 *
 * Can be called at any time after the ability has been registered.
 *
 * Example:
 *
 *     if ( wp_has_ability( 'other-plugin/some-ability' ) ) {
 *         wp_unregister_ability( 'other-plugin/some-ability' );
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::unregister()
 * @see wp_register_ability()
 *
 * @param string $name The name of the ability to unregister, including namespace prefix
 *                     (e.g., 'my-plugin/my-ability').
 * @return WP_Ability|null The unregistered ability instance on success, `null` on failure.
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
 * Use this for conditional logic and feature detection before attempting to
 * retrieve or use an ability.
 *
 * Example:
 *
 *     // Displays different UI based on available abilities.
 *     if ( wp_has_ability( 'premium-plugin/advanced-export' ) ) {
 *         echo 'Export with Premium Features';
 *     } else {
 *         echo 'Basic Export';
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::is_registered()
 * @see wp_get_ability()
 *
 * @param string $name The name of the ability to check, including namespace prefix
 *                     (e.g., 'my-plugin/my-ability').
 * @return bool `true` if the ability is registered, `false` otherwise.
 */
function wp_has_ability( string $name ): bool {
	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return false;
	}

	return $registry->is_registered( $name );
}

/**
 * Retrieves a registered ability.
 *
 * Returns the ability instance for inspection or use. The instance provides access
 * to the ability's configuration, metadata, and execution methods.
 *
 * Example:
 *
 *     // Prints information about a registered ability.
 *     $ability = wp_get_ability( 'my-plugin/export-data' );
 *     if ( $ability ) {
 *         echo $ability->get_label() . ': ' . $ability->get_description();
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::get_registered()
 * @see wp_has_ability()
 *
 * @param string $name The name of the ability, including namespace prefix
 *                     (e.g., 'my-plugin/my-ability').
 * @return WP_Ability|null The registered ability instance, or `null` if not registered.
 */
function wp_get_ability( string $name ): ?WP_Ability {
	$registry = WP_Abilities_Registry::get_instance();
	if ( null === $registry ) {
		return null;
	}

	return $registry->get_registered( $name );
}

/**
 * Retrieves all registered abilities.
 *
 * Returns an array of all ability instances currently registered in the system.
 * Use this for discovery, debugging, or building administrative interfaces.
 *
 * Example:
 *
 *     // Prints information about all available abilities.
 *     $abilities = wp_get_abilities();
 *     foreach ( $abilities as $ability ) {
 *         echo $ability->get_label() . ': ' . $ability->get_description() . "\n";
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry::get_all_registered()
 *
 * @return WP_Ability[] An array of registered WP_Ability instances. Returns an empty
 *                     array if no abilities are registered or if the registry is unavailable.
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
 * Ability categories provide a way to organize and group related abilities for better
 * discoverability and management. Ability categories must be registered before abilities
 * that reference them.
 *
 * Ability categories must be registered on the `wp_abilities_api_categories_init` action hook.
 *
 * Example:
 *
 *     function my_plugin_register_categories() {
 *         wp_register_ability_category(
 *             'content-management',
 *             array(
 *                 'label'       => __( 'Content Management', 'my-plugin' ),
 *                 'description' => __( 'Abilities for managing and organizing content.', 'my-plugin' ),
 *             )
 *         );
 *     }
 *     add_action( 'wp_abilities_api_categories_init', 'my_plugin_register_categories' );
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::register()
 * @see wp_register_ability()
 * @see wp_unregister_ability_category()
 *
 * @param string               $slug The unique slug for the ability category. Must contain only lowercase
 *                                   alphanumeric characters and dashes (e.g., 'data-export').
 * @param array<string, mixed> $args {
 *     An associative array of arguments for the ability category.
 *
 *     @type string               $label       Required. The human-readable label for the ability category.
 *     @type string               $description Required. A description of what abilities in this category do.
 *     @type array<string, mixed> $meta        Optional. Additional metadata for the ability category.
 * }
 * @return WP_Ability_Category|null The registered ability category instance on success, `null` on failure.
 */
function wp_register_ability_category( string $slug, array $args ): ?WP_Ability_Category {
	if ( ! doing_action( 'wp_abilities_api_categories_init' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: wp_abilities_api_categories_init, 2: ability category slug. */
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
 * Removes a previously registered ability category from the global registry. Use this to
 * disable ability categories that are no longer needed.
 *
 * Can be called at any time after the ability category has been registered.
 *
 * Example:
 *
 *     if ( wp_has_ability_category( 'deprecated-category' ) ) {
 *         wp_unregister_ability_category( 'deprecated-category' );
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::unregister()
 * @see wp_register_ability_category()
 *
 * @param string $slug The slug of the ability category to unregister.
 * @return WP_Ability_Category|null The unregistered ability category instance on success, `null` on failure.
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
 * Use this for conditional logic and feature detection before attempting to
 * retrieve or use an ability category.
 *
 * Example:
 *
 *     // Displays different UI based on available ability categories.
 *     if ( wp_has_ability_category( 'premium-features' ) ) {
 *         echo 'Premium Features Available';
 *     } else {
 *         echo 'Standard Features';
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::is_registered()
 * @see wp_get_ability_category()
 *
 * @param string $slug The slug of the ability category to check.
 * @return bool `true` if the ability category is registered, `false` otherwise.
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
 * Returns the ability category instance for inspection or use. The instance provides access
 * to the ability category's configuration and metadata.
 *
 * Example:
 *
 *     // Prints information about a registered ability category.
 *     $ability_category = wp_get_ability_category( 'content-management' );
 *     if ( $ability_category ) {
 *         echo $ability_category->get_label() . ': ' . $ability_category->get_description();
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::get_registered()
 * @see wp_has_ability_category()
 * @see wp_get_ability_categories()
 *
 * @param string $slug The slug of the ability category.
 * @return WP_Ability_Category|null The ability category instance, or `null` if not registered.
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
 * Returns an array of all ability category instances currently registered in the system.
 * Use this for discovery, debugging, or building administrative interfaces.
 *
 * Example:
 *
 *     // Prints information about all available ability categories.
 *     $ability_categories = wp_get_ability_categories();
 *     foreach ( $ability_categories as $ability_category ) {
 *         echo $ability_category->get_label() . ': ' . $ability_category->get_description() . "\n";
 *     }
 *
 * @since 6.9.0
 *
 * @see WP_Ability_Categories_Registry::get_all_registered()
 * @see wp_get_ability_category()
 *
 * @return WP_Ability_Category[] An array of registered ability category instances. Returns an empty array
 *                               if no ability categories are registered or if the registry is unavailable.
 */
function wp_get_ability_categories(): array {
	$registry = WP_Ability_Categories_Registry::get_instance();
	if ( null === $registry ) {
		return array();
	}

	return $registry->get_all_registered();
}
