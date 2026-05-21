<?php
/**
 * Abilities API
 *
 * Defines WP_Ability class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Encapsulates the properties and methods related to a specific ability in the registry.
 *
 * @since 6.9.0
 *
 * @see WP_Abilities_Registry
 */
class WP_Ability {

	/**
	 * The default value for the `show_in_rest` meta.
	 *
	 * @since 6.9.0
	 * @var bool
	 */
	protected const DEFAULT_SHOW_IN_REST = false;

	/**
	 * The default ability annotations.
	 * They are not guaranteed to provide a faithful description of ability behavior.
	 *
	 * @since 6.9.0
	 * @var array<string, bool|null>
	 */
	protected static $default_annotations = array(
		// If true, the ability does not modify its environment.
		'readonly'    => null,
		/*
		 * If true, the ability may perform destructive updates to its environment.
		 * If false, the ability performs only additive updates.
		 */
		'destructive' => null,
		/*
		 * If true, calling the ability repeatedly with the same arguments will have no additional effect
		 * on its environment.
		 */
		'idempotent'  => null,
	);

	/**
	 * The name of the ability, with its namespace.
	 * Example: `my-plugin/my-ability`.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $name;

	/**
	 * The human-readable ability label.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $label;

	/**
	 * The detailed ability description.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $description;

	/**
	 * The ability category.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $category;

	/**
	 * The optional ability input schema.
	 *
	 * @since 6.9.0
	 * @var array<string, mixed>
	 */
	protected $input_schema = array();

	/**
	 * The optional ability output schema.
	 *
	 * @since 6.9.0
	 * @var array<string, mixed>
	 */
	protected $output_schema = array();

	/**
	 * The ability execute callback.
	 *
	 * @since 6.9.0
	 * @var callable(mixed): (mixed|WP_Error)
	 */
	protected $execute_callback;

	/**
	 * The optional ability permission callback.
	 *
	 * @since 6.9.0
	 * @var callable(mixed): (bool|WP_Error)
	 */
	protected $permission_callback;

	/**
	 * The optional ability metadata.
	 *
	 * @since 6.9.0
	 * @var array<string, mixed>
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 * Do not use this constructor directly. Instead, use the `wp_register_ability()` function.
	 *
	 * @access private
	 *
	 * @since 6.9.0
	 *
	 * @see wp_register_ability()
	 *
	 * @param string               $name The name of the ability, with its namespace.
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
	 *         @type bool                     $show_in_rest Optional. Whether to expose this ability in the REST API. Default false.
	 *     }
	 * }
	 */
	public function __construct( string $name, array $args ) {
		$this->name = $name;

		$properties = $this->prepare_properties( $args );

		foreach ( $properties as $property_name => $property_value ) {
			if ( ! property_exists( $this, $property_name ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: Property name. */
						__( 'Property "%1$s" is not a valid property for ability "%2$s". Please check the %3$s class for allowed properties.' ),
						'<code>' . esc_html( $property_name ) . '</code>',
						'<code>' . esc_html( $this->name ) . '</code>',
						'<code>' . __CLASS__ . '</code>'
					),
					'6.9.0'
				);
				continue;
			}

			$this->$property_name = $property_value;
		}
	}

	/**
	 * Prepares and validates the properties used to instantiate the ability.
	 *
	 * Errors are thrown as exceptions instead of WP_Errors to allow for simpler handling and overloading. They are then
	 * caught and converted to a WP_Error by WP_Abilities_Registry::register().
	 *
	 * @since 6.9.0
	 *
	 * @see WP_Abilities_Registry::register()
	 *
	 * @param array<string, mixed> $args {
	 *     An associative array of arguments used to instantiate the ability class.
	 *
	 *     @type string               $label                 The human-readable label for the ability.
	 *     @type string               $description           A detailed description of what the ability does.
	 *     @type string               $category              The ability category slug this ability belongs to.
	 *     @type callable             $execute_callback      A callback function to execute when the ability is invoked.
	 *                                                       Receives optional mixed input and returns mixed result or WP_Error.
	 *     @type callable             $permission_callback   A callback function to check permissions before execution.
	 *                                                       Receives optional mixed input and returns bool or WP_Error.
	 *     @type array<string, mixed> $input_schema          Optional. JSON Schema definition for the ability's input. Required if ability accepts an input.
	 *     @type array<string, mixed> $output_schema         Optional. JSON Schema definition for the ability's output.
	 *     @type array<string, mixed> $meta                  {
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
	 *         @type bool                     $show_in_rest Optional. Whether to expose this ability in the REST API. Default false.
	 *     }
	 * }
	 * @return array<string, mixed> {
	 *     An associative array of arguments with validated and prepared properties for the ability class.
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
	 *         Additional metadata for the ability.
	 *
	 *         @type array<string, bool|null> $annotations  {
	 *             Semantic annotations describing the ability's behavioral characteristics.
	 *             These annotations are hints for tooling and documentation.
	 *
	 *             @type bool|null $readonly    If true, the ability does not modify its environment.
	 *             @type bool|null $destructive If true, the ability may perform destructive updates to its environment.
	 *                                          If false, the ability performs only additive updates.
	 *             @type bool|null $idempotent  If true, calling the ability repeatedly with the same arguments
	 *                                          will have no additional effect on its environment.
	 *         }
	 *         @type bool                     $show_in_rest Whether to expose this ability in the REST API. Default false.
	 *     }
	 * }
	 * @throws InvalidArgumentException if an argument is invalid.
	 */
	protected function prepare_properties( array $args ): array {
		// Required args must be present and of the correct type.
		if ( empty( $args['label'] ) || ! is_string( $args['label'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a `label` string.' )
			);
		}

		if ( empty( $args['description'] ) || ! is_string( $args['description'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a `description` string.' )
			);
		}

		if ( empty( $args['category'] ) || ! is_string( $args['category'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a `category` string.' )
			);
		}

		// If we are not overriding `ability_class` parameter during instantiation, then we need to validate the execute_callback.
		if ( get_class( $this ) === self::class && ( empty( $args['execute_callback'] ) || ! is_callable( $args['execute_callback'] ) ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a valid `execute_callback` function.' )
			);
		}

		// If we are not overriding `ability_class` parameter during instantiation, then we need to validate the permission_callback.
		if ( get_class( $this ) === self::class && ( empty( $args['permission_callback'] ) || ! is_callable( $args['permission_callback'] ) ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must provide a valid `permission_callback` function.' )
			);
		}

		// Optional args only need to be of the correct type if they are present.
		if ( isset( $args['input_schema'] ) && ! is_array( $args['input_schema'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties should provide a valid `input_schema` definition.' )
			);
		}

		if ( isset( $args['output_schema'] ) && ! is_array( $args['output_schema'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties should provide a valid `output_schema` definition.' )
			);
		}

		if ( isset( $args['meta'] ) && ! is_array( $args['meta'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties should provide a valid `meta` array.' )
			);
		}

		if ( isset( $args['meta']['annotations'] ) && ! is_array( $args['meta']['annotations'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability meta should provide a valid `annotations` array.' )
			);
		}

		if ( isset( $args['meta']['show_in_rest'] ) && ! is_bool( $args['meta']['show_in_rest'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability meta should provide a valid `show_in_rest` boolean.' )
			);
		}

		// Set defaults for optional meta.
		$args['meta']                = wp_parse_args(
			$args['meta'] ?? array(),
			array(
				'annotations'  => static::$default_annotations,
				'show_in_rest' => self::DEFAULT_SHOW_IN_REST,
			)
		);
		$args['meta']['annotations'] = wp_parse_args(
			$args['meta']['annotations'],
			static::$default_annotations
		);

		return $args;
	}

	/**
	 * Retrieves the name of the ability, with its namespace.
	 * Example: `my-plugin/my-ability`.
	 *
	 * @since 6.9.0
	 *
	 * @return string The ability name, with its namespace.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Retrieves the human-readable label for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return string The human-readable ability label.
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Retrieves the detailed description for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return string The detailed description for the ability.
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Retrieves the ability category for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return string The ability category for the ability.
	 */
	public function get_category(): string {
		return $this->category;
	}

	/**
	 * Retrieves the input schema for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> The input schema for the ability.
	 */
	public function get_input_schema(): array {
		return $this->input_schema;
	}

	/**
	 * Retrieves the output schema for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> The output schema for the ability.
	 */
	public function get_output_schema(): array {
		return $this->output_schema;
	}

	/**
	 * Retrieves the metadata for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> The metadata for the ability.
	 */
	public function get_meta(): array {
		return $this->meta;
	}

	/**
	 * Retrieves a specific metadata item for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @param string $key           The metadata key to retrieve.
	 * @param mixed  $default_value Optional. The default value to return if the metadata item is not found. Default `null`.
	 * @return mixed The value of the metadata item, or the default value if not found.
	 */
	public function get_meta_item( string $key, $default_value = null ) {
		return array_key_exists( $key, $this->meta ) ? $this->meta[ $key ] : $default_value;
	}

	/**
	 * Normalizes the input for the ability, applying the default value from the input schema when needed.
	 *
	 * When no input is provided and the input schema is defined with a top-level `default` key, this method returns
	 * the value of that key. If the input schema does not define a `default`, or if the input schema is empty,
	 * this method returns null. If input is provided, it is returned as-is.
	 *
	 * The {@see 'wp_ability_normalize_input'} filter fires after the built-in default-value handling,
	 * allowing plugins to transform the result.
	 *
	 * @since 6.9.0
	 * @since 7.1.0 Added the `wp_ability_normalize_input` filter.
	 *
	 * @param mixed $input Optional. The raw input provided for the ability. Default `null`.
	 * @return mixed The normalized input, or a `WP_Error` if a filter returned one.
	 */
	public function normalize_input( $input = null ) {
		if ( null === $input ) {
			$input_schema = $this->get_input_schema();
			if ( array_key_exists( 'default', $input_schema ) ) {
				$input = $input_schema['default'];
			}
		}

		/**
		 * Filters the normalized input for an ability.
		 *
		 * Fires after `normalize_input()` has applied any default value declared in the input schema,
		 * giving plugins a chance to adjust the input before it is consumed downstream. Common uses
		 * include defaulting beyond what JSON Schema can express, prompt enrichment, and injecting
		 * caller metadata.
		 *
		 * Returning a `WP_Error` causes callers that propagate it (such as `execute()`) to halt
		 * before validation, permission checks, and the registered execute callback.
		 *
		 * @since 7.1.0
		 *
		 * @param mixed      $input        The normalized input data.
		 * @param string     $ability_name The name of the ability.
		 * @param WP_Ability $ability      The ability instance.
		 */
		return apply_filters( 'wp_ability_normalize_input', $input, $this->name, $this );
	}

	/**
	 * Validates input data against the input schema.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input Optional. The input data to validate. Default `null`.
	 * @return true|WP_Error Returns true if valid or the WP_Error object if validation fails.
	 */
	public function validate_input( $input = null ) {
		$input_schema = $this->get_input_schema();
		if ( empty( $input_schema ) ) {
			if ( null === $input ) {
				return true;
			}

			return new WP_Error(
				'ability_missing_input_schema',
				sprintf(
					/* translators: %s ability name. */
					__( 'Ability "%s" does not define an input schema required to validate the provided input.' ),
					$this->name
				)
			);
		}

		$valid_input = rest_validate_value_from_schema( $input, $input_schema, 'input' );
		if ( is_wp_error( $valid_input ) ) {
			$is_valid = new WP_Error(
				'ability_invalid_input',
				sprintf(
					/* translators: %1$s ability name, %2$s error message. */
					__( 'Ability "%1$s" has invalid input. Reason: %2$s' ),
					$this->name,
					$valid_input->get_error_message()
				)
			);
		} else {
			$is_valid = true;
		}

		/**
		 * Filters the input validation result for an ability.
		 *
		 * Allows developers to add custom validation logic on top of the default
		 * JSON Schema validation. If default validation already failed, the filter
		 * receives the WP_Error object and can add additional error information or
		 * override it. If default validation passed, the filter can add additional
		 * validation checks and return a WP_Error if those checks fail.
		 *
		 * @since 7.1.0
		 *
		 * @param true|WP_Error $is_valid     The validation result from default validation.
		 * @param mixed         $input        The input data being validated.
		 * @param string        $ability_name The name of the ability.
		 */
		$validity = apply_filters( 'wp_ability_validate_input', $is_valid, $input, $this->name );
		if ( false === $validity ) {
			return new WP_Error( 'ability_invalid_input', __( 'Invalid input.' ) );
		}
		if ( is_wp_error( $validity ) && $validity->has_errors() ) {
			return $validity;
		}
		return true;
	}

	/**
	 * Invokes a callable, ensuring the input is passed through only if the input schema is defined.
	 *
	 * @since 6.9.0
	 *
	 * @param callable $callback The callable to invoke.
	 * @param mixed    $input    Optional. The input data for the ability. Default `null`.
	 * @return mixed The result of the callable execution, or a `WP_Error` if the callback threw.
	 */
	protected function invoke_callback( callable $callback, $input = null ) {
		$args = array();
		if ( ! empty( $this->get_input_schema() ) ) {
			$args[] = $input;
		}

		try {
			return $callback( ...$args );
		} catch ( Throwable $e ) {
			return new WP_Error(
				'ability_callback_exception',
				sprintf(
					/* translators: 1: Ability name, 2: Exception message. */
					__( 'Ability "%1$s" callback threw an exception: %2$s' ),
					$this->name,
					esc_html( $e->getMessage() )
				)
			);
		}
	}

	/**
	 * Checks whether the ability has the necessary permissions.
	 *
	 * Please note that input is not automatically validated against the input schema.
	 * Use `validate_input()` method to validate input before calling this method if needed.
	 *
	 * The {@see 'wp_ability_permission_result'} filter fires after the registered
	 * `permission_callback` returns, allowing plugins to override the result.
	 *
	 * @since 6.9.0
	 * @since 7.1.0 Added the `wp_ability_permission_result` filter.
	 *
	 * @see validate_input()
	 *
	 * @param mixed $input Optional. The valid input data for permission checking. Default `null`.
	 * @return bool|WP_Error Whether the ability has the necessary permission.
	 */
	public function check_permissions( $input = null ) {
		if ( ! is_callable( $this->permission_callback ) ) {
			return new WP_Error(
				'ability_invalid_permission_callback',
				/* translators: %s ability name. */
				sprintf( __( 'Ability "%s" does not have a valid permission callback.' ), $this->name )
			);
		}

		$permission = $this->invoke_callback( $this->permission_callback, $input );

		/**
		 * Filters the result of an ability's permission check.
		 *
		 * Fires after the registered `permission_callback` returns. Plugins can use this to layer
		 * additional authorization rules on top of the ability's own permission logic — for example,
		 * multi-factor authorization gates or temporary permission elevation for trusted contexts.
		 *
		 * Filters can return `true` to grant, `false` to deny, or a `WP_Error` to deny with a specific
		 * error code and message. The filter receives whatever the `permission_callback` produced.
		 * Any other return value is coerced to `false`.
		 *
		 * @since 7.1.0
		 *
		 * @param bool|WP_Error $permission   The permission result returned by `permission_callback`.
		 * @param string        $ability_name The name of the ability.
		 * @param mixed         $input        The input data for the permission check.
		 * @param WP_Ability    $ability      The ability instance.
		 */
		$result = apply_filters( 'wp_ability_permission_result', $permission, $this->name, $input, $this );
		if ( ! is_bool( $result ) && ! is_wp_error( $result ) ) {
			$result = false;
		}
		return $result;
	}

	/**
	 * Executes the ability callback.
	 *
	 * The {@see 'wp_ability_execute_result'} filter fires before this method returns, allowing
	 * plugins to transform the result produced by the registered `execute_callback`.
	 *
	 * @since 6.9.0
	 * @since 7.1.0 Added the `wp_ability_execute_result` filter.
	 *
	 * @param mixed $input Optional. The input data for the ability. Default `null`.
	 * @return mixed|WP_Error The result of the ability execution, or WP_Error on failure.
	 */
	protected function do_execute( $input = null ) {
		if ( ! is_callable( $this->execute_callback ) ) {
			$result = new WP_Error(
				'ability_invalid_execute_callback',
				/* translators: %s ability name. */
				sprintf( __( 'Ability "%s" does not have a valid execute callback.' ), $this->name )
			);
		} else {
			$result = $this->invoke_callback( $this->execute_callback, $input );
		}

		/**
		 * Filters the result returned by an ability's execute callback.
		 *
		 * Fires after the registered execute callback runs. Plugins can use this to transform the
		 * result — response formatting, stripping internal metadata, content safety filtering,
		 * response enrichment, or recovering from a failure by returning a successful value.
		 *
		 * The filter receives whatever the registered callback produced, including a `WP_Error`
		 * if execution failed. Filters may pass the `WP_Error` through unchanged, override it with
		 * a recovered result, or convert a successful result into a `WP_Error`.
		 *
		 * @since 7.1.0
		 *
		 * @param mixed      $result       The result returned by the registered `execute_callback`,
		 *                                 or a `WP_Error` if execution failed.
		 * @param string     $ability_name The name of the ability.
		 * @param mixed      $input        The normalized input data.
		 * @param WP_Ability $ability      The ability instance.
		 */
		return apply_filters( 'wp_ability_execute_result', $result, $this->name, $input, $this );
	}

	/**
	 * Validates output data against the output schema.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $output The output data to validate.
	 * @return true|WP_Error Returns true if valid, or a WP_Error object if validation fails.
	 */
	protected function validate_output( $output ) {
		$output_schema = $this->get_output_schema();
		if ( empty( $output_schema ) ) {
			$is_valid = true;
		} else {
			$valid_output = rest_validate_value_from_schema( $output, $output_schema, 'output' );
			if ( is_wp_error( $valid_output ) ) {
				$is_valid = new WP_Error(
					'ability_invalid_output',
					sprintf(
						/* translators: %1$s ability name, %2$s error message. */
						__( 'Ability "%1$s" has invalid output. Reason: %2$s' ),
						$this->name,
						$valid_output->get_error_message()
					)
				);
			} else {
				$is_valid = true;
			}
		}

		/**
		 * Filters the output validation result for an ability.
		 *
		 * Allows developers to add custom validation logic on top of the default
		 * JSON Schema validation. If default validation already failed, the filter
		 * receives the WP_Error object and can add additional error information or
		 * override it. If default validation passed, the filter can add additional
		 * validation checks and return a WP_Error if those checks fail.
		 *
		 * @since 7.1.0
		 *
		 * @param true|WP_Error $is_valid     The validation result from default validation.
		 * @param mixed         $output       The output data being validated.
		 * @param string        $ability_name The name of the ability.
		 */
		$validity = apply_filters( 'wp_ability_validate_output', $is_valid, $output, $this->name );
		if ( false === $validity ) {
			return new WP_Error( 'ability_invalid_output', __( 'Invalid output.' ) );
		}
		if ( is_wp_error( $validity ) && $validity->has_errors() ) {
			return $validity;
		}
		return true;
	}

	/**
	 * Executes the ability after input validation and running a permission check.
	 * Before returning the return value, it also validates the output.
	 *
	 * @since 6.9.0
	 * @since 7.1.0 Added the `wp_pre_execute_ability` filter.
	 *
	 * @param mixed $input Optional. The input data for the ability. Default `null`.
	 * @return mixed|WP_Error The result of the ability execution, or WP_Error on failure.
	 */
	public function execute( $input = null ) {
		/**
		 * Filters whether to short-circuit ability execution.
		 *
		 * Returning a value other than the received default bypasses the rest of `execute()` —
		 * input normalization, input validation, permission checks, the registered execute callback,
		 * output validation, and the surrounding actions — and the value is returned to the caller
		 * as-is. Useful for cached responses, rate limiting, maintenance mode, and test mocking.
		 *
		 * To continue with normal execution, return `$pre` unchanged. This preserves any value
		 * (including `null`, `false`, or arbitrary objects) as a valid short-circuit result.
		 *
		 * Because validation is bypassed, callers that short-circuit are responsible for the
		 * integrity of any value they consume from `$input`.
		 *
		 * @since 7.1.0
		 *
		 * @param mixed      $pre          The pre-computed result. Return this value unchanged to continue execution.
		 *                                 Default `WP_Filter_Sentinel` instance unique to this invocation.
		 * @param string     $ability_name The name of the ability.
		 * @param mixed      $input        The raw input passed to `execute()`.
		 * @param WP_Ability $ability      The ability instance.
		 */
		$pre_execute_sentinel = new WP_Filter_Sentinel();
		$pre                  = apply_filters( 'wp_pre_execute_ability', $pre_execute_sentinel, $this->name, $input, $this );
		if ( $pre !== $pre_execute_sentinel ) {
			return $pre;
		}

		$input = $this->normalize_input( $input );
		if ( is_wp_error( $input ) ) {
			return $input;
		}

		$is_valid = $this->validate_input( $input );
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		$has_permissions = $this->check_permissions( $input );
		if ( true !== $has_permissions ) {
			if ( is_wp_error( $has_permissions ) ) {
				// Don't leak the permission check error to someone without the correct perms.
				_doing_it_wrong(
					__METHOD__,
					esc_html( $has_permissions->get_error_message() ),
					'6.9.0'
				);
			}

			return new WP_Error(
				'ability_invalid_permissions',
				/* translators: %s ability name. */
				sprintf( __( 'Ability "%s" does not have necessary permission.' ), $this->name )
			);
		}

		/**
		 * Fires before an ability gets executed, after input validation and permissions check.
		 *
		 * @since 6.9.0
		 *
		 * @param string $ability_name The name of the ability.
		 * @param mixed  $input        The input data for the ability.
		 */
		do_action( 'wp_before_execute_ability', $this->name, $input );

		$result = $this->do_execute( $input );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$is_valid = $this->validate_output( $result );
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		/**
		 * Fires immediately after an ability finished executing.
		 *
		 * @since 6.9.0
		 *
		 * @param string $ability_name The name of the ability.
		 * @param mixed  $input        The input data for the ability.
		 * @param mixed  $result       The result of the ability execution.
		 */
		do_action( 'wp_after_execute_ability', $this->name, $input, $result );

		return $result;
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the ability object is unserialized.
	 *                        This is a security hardening measure to prevent unserialization of the ability.
	 */
	public function __wakeup(): void {
		throw new LogicException( __CLASS__ . ' should never be unserialized.' );
	}

	/**
	 * Sleep magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the ability object is serialized.
	 *                        This is a security hardening measure to prevent serialization of the ability.
	 */
	public function __sleep(): array {
		throw new LogicException( __CLASS__ . ' should never be serialized.' );
	}
}
