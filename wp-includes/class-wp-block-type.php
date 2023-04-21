<?php
/**
 * Blocks API: WP_Block_Type class
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 5.0.0
 */

/**
 * Core class representing a block type.
 *
 * @since 5.0.0
 *
 * @see register_block_type()
 */
#[AllowDynamicProperties]
class WP_Block_Type {

	/**
	 * Block API version.
	 *
	 * @since 5.6.0
	 * @var int
	 */
	public $api_version = 1;

	/**
	 * Block type key.
	 *
	 * @since 5.0.0
	 * @var string
	 */
	public $name;

	/**
	 * Human-readable block type label.
	 *
	 * @since 5.5.0
	 * @var string
	 */
	public $title = '';

	/**
	 * Block type category classification, used in search interfaces
	 * to arrange block types by category.
	 *
	 * @since 5.5.0
	 * @var string|null
	 */
	public $category = null;

	/**
	 * Setting parent lets a block require that it is only available
	 * when nested within the specified blocks.
	 *
	 * @since 5.5.0
	 * @var string[]|null
	 */
	public $parent = null;

	/**
	 * Setting ancestor makes a block available only inside the specified
	 * block types at any position of the ancestor's block subtree.
	 *
	 * @since 6.0.0
	 * @var string[]|null
	 */
	public $ancestor = null;

	/**
	 * Block type icon.
	 *
	 * @since 5.5.0
	 * @var string|null
	 */
	public $icon = null;

	/**
	 * A detailed block type description.
	 *
	 * @since 5.5.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Additional keywords to produce block type as result
	 * in search interfaces.
	 *
	 * @since 5.5.0
	 * @var string[]
	 */
	public $keywords = array();

	/**
	 * The translation textdomain.
	 *
	 * @since 5.5.0
	 * @var string|null
	 */
	public $textdomain = null;

	/**
	 * Alternative block styles.
	 *
	 * @since 5.5.0
	 * @var array
	 */
	public $styles = array();

	/**
	 * Block variations.
	 *
	 * @since 5.8.0
	 * @var array[]
	 */
	public $variations = array();

	/**
	 * Custom CSS selectors for theme.json style generation.
	 *
	 * @since 6.3.0
	 * @var array
	 */
	public $selectors = array();

	/**
	 * Supported features.
	 *
	 * @since 5.5.0
	 * @var array|null
	 */
	public $supports = null;

	/**
	 * Structured data for the block preview.
	 *
	 * @since 5.5.0
	 * @var array|null
	 */
	public $example = null;

	/**
	 * Block type render callback.
	 *
	 * @since 5.0.0
	 * @var callable
	 */
	public $render_callback = null;

	/**
	 * Block type attributes property schemas.
	 *
	 * @since 5.0.0
	 * @var array|null
	 */
	public $attributes = null;

	/**
	 * Context values inherited by blocks of this type.
	 *
	 * @since 5.5.0
	 * @var string[]
	 */
	public $uses_context = array();

	/**
	 * Context provided by blocks of this type.
	 *
	 * @since 5.5.0
	 * @var string[]|null
	 */
	public $provides_context = null;

	/**
	 * Block type editor only script handles.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	public $editor_script_handles = array();

	/**
	 * Block type front end and editor script handles.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	public $script_handles = array();

	/**
	 * Block type front end only script handles.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	public $view_script_handles = array();

	/**
	 * Block type editor only style handles.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	public $editor_style_handles = array();

	/**
	 * Block type front end and editor style handles.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	public $style_handles = array();

	/**
	 * Deprecated block type properties for script and style handles.
	 *
	 * @since 6.1.0
	 * @var string[]
	 */
	private $deprecated_properties = array(
		'editor_script',
		'script',
		'view_script',
		'editor_style',
		'style',
	);

	/**
	 * Attributes supported by every block.
	 *
	 * @since 6.0.0
	 * @var array
	 */
	const GLOBAL_ATTRIBUTES = array(
		'lock' => array( 'type' => 'object' ),
	);

	/**
	 * Constructor.
	 *
	 * Will populate object properties from the provided arguments.
	 *
	 * @since 5.0.0
	 * @since 5.5.0 Added the `title`, `category`, `parent`, `icon`, `description`,
	 *              `keywords`, `textdomain`, `styles`, `supports`, `example`,
	 *              `uses_context`, and `provides_context` properties.
	 * @since 5.6.0 Added the `api_version` property.
	 * @since 5.8.0 Added the `variations` property.
	 * @since 5.9.0 Added the `view_script` property.
	 * @since 6.0.0 Added the `ancestor` property.
	 * @since 6.1.0 Added the `editor_script_handles`, `script_handles`, `view_script_handles,
	 *              `editor_style_handles`, and `style_handles` properties.
	 *              Deprecated the `editor_script`, `script`, `view_script`, `editor_style`, and `style` properties.
	 * @since 6.3.0 Added the `selectors` property.
	 *
	 * @see register_block_type()
	 *
	 * @param string       $block_type Block type name including namespace.
	 * @param array|string $args       {
	 *     Optional. Array or string of arguments for registering a block type. Any arguments may be defined,
	 *     however the ones described below are supported by default. Default empty array.
	 *
	 *     @type string        $api_version              Block API version.
	 *     @type string        $title                    Human-readable block type label.
	 *     @type string|null   $category                 Block type category classification, used in
	 *                                                   search interfaces to arrange block types by category.
	 *     @type string[]|null $parent                   Setting parent lets a block require that it is only
	 *                                                   available when nested within the specified blocks.
	 *     @type string[]|null $ancestor                 Setting ancestor makes a block available only inside the specified
	 *                                                   block types at any position of the ancestor's block subtree.
	 *     @type string|null   $icon                     Block type icon.
	 *     @type string        $description              A detailed block type description.
	 *     @type string[]      $keywords                 Additional keywords to produce block type as
	 *                                                   result in search interfaces.
	 *     @type string|null   $textdomain               The translation textdomain.
	 *     @type array[]       $styles                   Alternative block styles.
	 *     @type array[]       $variations               Block variations.
	 *     @type array         $selectors                Custom CSS selectors for theme.json style generation.
	 *     @type array|null    $supports                 Supported features.
	 *     @type array|null    $example                  Structured data for the block preview.
	 *     @type callable|null $render_callback          Block type render callback.
	 *     @type array|null    $attributes               Block type attributes property schemas.
	 *     @type string[]      $uses_context             Context values inherited by blocks of this type.
	 *     @type string[]|null $provides_context         Context provided by blocks of this type.
	 *     @type string[]      $editor_script_handles    Block type editor only script handles.
	 *     @type string[]      $script_handles           Block type front end and editor script handles.
	 *     @type string[]      $view_script_handles      Block type front end only script handles.
	 *     @type string[]      $editor_style_handles     Block type editor only style handles.
	 *     @type string[]      $style_handles            Block type front end and editor style handles.
	 * }
	 */
	public function __construct( $block_type, $args = array() ) {
		$this->name = $block_type;

		$this->set_props( $args );
	}

	/**
	 * Proxies getting values for deprecated properties for script and style handles for backward compatibility.
	 * Gets the value for the corresponding new property if the first item in the array provided.
	 *
	 * @since 6.1.0
	 *
	 * @param string $name Deprecated property name.
	 *
	 * @return string|string[]|null|void The value read from the new property if the first item in the array provided,
	 *                                   null when value not found, or void when unknown property name provided.
	 */
	public function __get( $name ) {
		if ( ! in_array( $name, $this->deprecated_properties, true ) ) {
			return;
		}

		$new_name = $name . '_handles';

		if ( ! property_exists( $this, $new_name ) || ! is_array( $this->{$new_name} ) ) {
			return null;
		}

		if ( count( $this->{$new_name} ) > 1 ) {
			return $this->{$new_name};
		}
		return isset( $this->{$new_name}[0] ) ? $this->{$new_name}[0] : null;
	}

	/**
	 * Proxies checking for deprecated properties for script and style handles for backward compatibility.
	 * Checks whether the corresponding new property has the first item in the array provided.
	 *
	 * @since 6.1.0
	 *
	 * @param string $name Deprecated property name.
	 *
	 * @return boolean Returns true when for the new property the first item in the array exists,
	 *                     or false otherwise.
	 */
	public function __isset( $name ) {
		if ( ! in_array( $name, $this->deprecated_properties, true ) ) {
			return false;
		}

		$new_name = $name . '_handles';
		return isset( $this->{$new_name}[0] );
	}

	/**
	 * Proxies setting values for deprecated properties for script and style handles for backward compatibility.
	 * Sets the value for the corresponding new property as the first item in the array.
	 * It also allows setting custom properties for backward compatibility.
	 *
	 * @since 6.1.0
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 */
	public function __set( $name, $value ) {
		if ( ! in_array( $name, $this->deprecated_properties, true ) ) {
			$this->{$name} = $value;
			return;
		}

		$new_name = $name . '_handles';

		if ( is_array( $value ) ) {
			$filtered = array_filter( $value, 'is_string' );

			if ( count( $filtered ) !== count( $value ) ) {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: %s: The '$value' argument. */
							__( 'The %s argument must be a string or a string array.' ),
							'<code>$value</code>'
						),
						'6.1.0'
					);
			}

			$this->{$new_name} = array_values( $filtered );
			return;
		}

		if ( ! is_string( $value ) ) {
			return;
		}

		$this->{$new_name} = array( $value );
	}

	/**
	 * Renders the block type output for given attributes.
	 *
	 * @since 5.0.0
	 *
	 * @param array  $attributes Optional. Block attributes. Default empty array.
	 * @param string $content    Optional. Block content. Default empty string.
	 * @return string Rendered block type output.
	 */
	public function render( $attributes = array(), $content = '' ) {
		if ( ! $this->is_dynamic() ) {
			return '';
		}

		$attributes = $this->prepare_attributes_for_render( $attributes );

		return (string) call_user_func( $this->render_callback, $attributes, $content );
	}

	/**
	 * Returns true if the block type is dynamic, or false otherwise. A dynamic
	 * block is one which defers its rendering to occur on-demand at runtime.
	 *
	 * @since 5.0.0
	 *
	 * @return bool Whether block type is dynamic.
	 */
	public function is_dynamic() {
		return is_callable( $this->render_callback );
	}

	/**
	 * Validates attributes against the current block schema, populating
	 * defaulted and missing values.
	 *
	 * @since 5.0.0
	 *
	 * @param array $attributes Original block attributes.
	 * @return array Prepared block attributes.
	 */
	public function prepare_attributes_for_render( $attributes ) {
		// If there are no attribute definitions for the block type, skip
		// processing and return verbatim.
		if ( ! isset( $this->attributes ) ) {
			return $attributes;
		}

		foreach ( $attributes as $attribute_name => $value ) {
			// If the attribute is not defined by the block type, it cannot be
			// validated.
			if ( ! isset( $this->attributes[ $attribute_name ] ) ) {
				continue;
			}

			$schema = $this->attributes[ $attribute_name ];

			// Validate value by JSON schema. An invalid value should revert to
			// its default, if one exists. This occurs by virtue of the missing
			// attributes loop immediately following. If there is not a default
			// assigned, the attribute value should remain unset.
			$is_valid = rest_validate_value_from_schema( $value, $schema, $attribute_name );
			if ( is_wp_error( $is_valid ) ) {
				unset( $attributes[ $attribute_name ] );
			}
		}

		// Populate values of any missing attributes for which the block type
		// defines a default.
		$missing_schema_attributes = array_diff_key( $this->attributes, $attributes );
		foreach ( $missing_schema_attributes as $attribute_name => $schema ) {
			if ( isset( $schema['default'] ) ) {
				$attributes[ $attribute_name ] = $schema['default'];
			}
		}

		return $attributes;
	}

	/**
	 * Sets block type properties.
	 *
	 * @since 5.0.0
	 *
	 * @param array|string $args Array or string of arguments for registering a block type.
	 *                           See WP_Block_Type::__construct() for information on accepted arguments.
	 */
	public function set_props( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'render_callback' => null,
			)
		);

		$args['name'] = $this->name;

		// Setup attributes if needed.
		if ( ! isset( $args['attributes'] ) || ! is_array( $args['attributes'] ) ) {
			$args['attributes'] = array();
		}

		// Register core attributes.
		foreach ( static::GLOBAL_ATTRIBUTES as $attr_key => $attr_schema ) {
			if ( ! array_key_exists( $attr_key, $args['attributes'] ) ) {
				$args['attributes'][ $attr_key ] = $attr_schema;
			}
		}

		/**
		 * Filters the arguments for registering a block type.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $args       Array of arguments for registering a block type.
		 * @param string $block_type Block type name including namespace.
		 */
		$args = apply_filters( 'register_block_type_args', $args, $this->name );

		foreach ( $args as $property_name => $property_value ) {
			$this->$property_name = $property_value;
		}
	}

	/**
	 * Get all available block attributes including possible layout attribute from Columns block.
	 *
	 * @since 5.0.0
	 *
	 * @return array Array of attributes.
	 */
	public function get_attributes() {
		return is_array( $this->attributes ) ?
			$this->attributes :
			array();
	}
}
