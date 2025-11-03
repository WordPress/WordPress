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
	 * @var callable( mixed $input= ): (mixed|WP_Error)
	 */
	protected $execute_callback;

	/**
	 * The optional ability permission callback.
	 *
	 * @since 6.9.0
	 * @var callable( mixed $input= ): (bool|WP_Error)
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
	 * caught and converted to a WP_Error when by WP_Abilities_Registry::register().
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

		if ( empty( $args['execute_callback'] ) || ! is_callable( $args['execute_callback'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a valid `execute_callback` function.' )
			);
		}

		if ( empty( $args['permission_callback'] ) || ! is_callable( $args['permission_callback'] ) ) {
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
	 * @since 6.9.0
	 *
	 * @param mixed $input Optional. The raw input provided for the ability. Default `null`.
	 * @return mixed The same input, or the default from schema, or `null` if default not set.
	 */
	public function normalize_input( $input = null ) {
		if ( null !== $input ) {
			return $input;
		}

		$input_schema = $this->get_input_schema();
		if ( ! empty( $input_schema ) && array_key_exists( 'default', $input_schema ) ) {
			return $input_schema['default'];
		}

		return null;
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
					esc_html( $this->name )
				)
			);
		}

		$valid_input = rest_validate_value_from_schema( $input, $input_schema, 'input' );
		if ( is_wp_error( $valid_input ) ) {
			return new WP_Error(
				'ability_invalid_input',
				sprintf(
					/* translators: %1$s ability name, %2$s error message. */
					__( 'Ability "%1$s" has invalid input. Reason: %2$s' ),
					esc_html( $this->name ),
					$valid_input->get_error_message()
				)
			);
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
	 * @return mixed The result of the callable execution.
	 */
	protected function invoke_callback( callable $callback, $input = null ) {
		$args = array();
		if ( ! empty( $this->get_input_schema() ) ) {
			$args[] = $input;
		}

		return $callback( ...$args );
	}

	/**
	 * Checks whether the ability has the necessary permissions.
	 *
	 * Please note that input is not automatically validated against the input schema.
	 * Use `validate_input()` method to validate input before calling this method if needed.
	 *
	 * @since 6.9.0
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
				sprintf( __( 'Ability "%s" does not have a valid permission callback.' ), esc_html( $this->name ) )
			);
		}

		return $this->invoke_callback( $this->permission_callback, $input );
	}

	/**
	 * Executes the ability callback.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input Optional. The input data for the ability. Default `null`.
	 * @return mixed|WP_Error The result of the ability execution, or WP_Error on failure.
	 */
	protected function do_execute( $input = null ) {
		if ( ! is_callable( $this->execute_callback ) ) {
			return new WP_Error(
				'ability_invalid_execute_callback',
				/* translators: %s ability name. */
				sprintf( __( 'Ability "%s" does not have a valid execute callback.' ), esc_html( $this->name ) )
			);
		}

		return $this->invoke_callback( $this->execute_callback, $input );
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
			return true;
		}

		$valid_output = rest_validate_value_from_schema( $output, $output_schema, 'output' );
		if ( is_wp_error( $valid_output ) ) {
			return new WP_Error(
				'ability_invalid_output',
				sprintf(
					/* translators: %1$s ability name, %2$s error message. */
					__( 'Ability "%1$s" has invalid output. Reason: %2$s' ),
					esc_html( $this->name ),
					$valid_output->get_error_message()
				)
			);
		}

		return true;
	}

	/**
	 * Executes the ability after input validation and running a permission check.
	 * Before returning the return value, it also validates the output.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input Optional. The input data for the ability. Default `null`.
	 * @return mixed|WP_Error The result of the ability execution, or WP_Error on failure.
	 */
	public function execute( $input = null ) {
		$input    = $this->normalize_input( $input );
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
				sprintf( __( 'Ability "%s" does not have necessary permission.' ), esc_html( $this->name ) )
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
