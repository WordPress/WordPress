<?php
/**
 * Blocks API: WP_Block class
 *
 * @package WordPress
 * @since 5.5.0
 */

/**
 * Class representing a parsed instance of a block.
 *
 * @since 5.5.0
 * @property array $attributes
 * @property array $context
 * @property WP_Block[] $inner_blocks;
 * @property string $inner_html;
 * @property array $inner_content;
 */
class WP_Block {

	/**
	 * Original parsed array representation of block.
	 *
	 * @since 5.5.0
	 * @var array
	 */
	public $parsed_block;

	/**
	 * All available context of the current hierarchy.
	 *
	 * @since 5.5.0
	 * @var array
	 * @access protected
	 */
	protected $available_context;

	/**
	 * Name of block.
	 *
	 * @example "core/paragraph"
	 *
	 * @since 5.5.0
	 * @var string
	 */
	public $name;

	/**
	 * Block type associated with the instance.
	 *
	 * @since 5.5.0
	 * @var WP_Block_Type
	 */
	public $block_type;

	/**
	 * Map of block property names and their cached value.
	 *
	 * Some block properties are computed lazily using a getter function. The
	 * result is then cached here for subsequent use.
	 *
	 * @since 5.6.0
	 * @var array
	 */
	protected $cached_properties = array();

	/**
	 * Creates a block instance from a backing `$parsed_block` array and list of
	 * `$available_context`. From these, the block's dynamic properties can be
	 * derived.
	 *
	 * The given array of context values will not necessarily be available on
	 * the instance itself, but is treated as the full set of values provided by
	 * the block's ancestry. This is assigned to the private `available_context`
	 * property. Only values which are configured to consumed by the block via
	 * its registered type will be assigned to the block's `context` property.
	 *
	 * @since 5.5.0
	 *
	 * @param array                  $parsed_block      Array of parsed block properties.
	 * @param array                  $available_context Optional array of ancestry context values.
	 * @param WP_Block_Type_Registry $registry          Optional block type registry.
	 */
	public function __construct( $parsed_block, $available_context = array(), $registry = null ) {
		if ( is_null( $registry ) ) {
			$this->registry = WP_Block_Type_Registry::get_instance();
		} else {
			$this->registry = $registry;
		}

		$this->reset( $parsed_block, $available_context );
	}

	/**
	 * Changes the backing `$parsed_block` and `$available_context` used to
	 * derive the block's dynamic properties.
	 *
	 * @since 5.6.0

	 * @param array $parsed_block      Array of parsed block properties.
	 * @param array $available_context Optional array of ancestry context values.
	 * @param array $cached_properties Optional cache of dynamic properties to use.
	 */
	protected function reset(
		$parsed_block,
		$available_context = array(),
		$cached_properties = array()
	) {
		$this->parsed_block      = $parsed_block;
		$this->available_context = $available_context;
		$this->name              = $parsed_block['blockName'];
		$this->block_type        = $this->registry->get_registered( $this->name );
		$this->cached_properties = $cached_properties;
	}

	/**
	 * Getter used for the block's dynamic properties:
	 *
	 * - `$block->attributes`
	 * - `$block->context`
	 * - `$block->inner_blocks`
	 * - `$block->inner_html`
	 * - `$block->inner_content`
	 *
	 * Each dynamic property is obtained by calling the associated getter
	 * function (e.g. `this->get_attributes()`). The result is then cached in
	 * `$this->cached_attributes` for subsequent calls.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name Property name.
	 * @return array|null Prepared attributes, or null.
	 */
	public function __get( $name ) {
		if ( method_exists( $this, "get_$name" ) ) {
			if ( ! isset( $this->cached_properties[ $name ] ) ) {
				$this->cached_properties[ $name ] = $this->{"get_$name"}();
			}

			return $this->cached_properties[ $name ];
		}

		return null;
	}

	/**
	 * Block attributes.
	 *
	 * Use `$block->attributes` to access this.
	 *
	 * @since 5.6.0
	 * @return array
	 */
	protected function get_attributes() {
		$attributes = isset( $this->parsed_block['attrs'] ) ?
			$this->parsed_block['attrs'] :
			array();

		if ( ! is_null( $this->block_type ) ) {
			return $this->block_type->prepare_attributes_for_render( $attributes );
		}

		return $attributes;
	}

	/**
	 * Block context values.
	 *
	 * Use `$block->context` to access this.
	 *
	 * @since 5.6.0
	 * @return array
	 */
	protected function get_context() {
		$context = array();

		if ( ! empty( $this->block_type->uses_context ) ) {
			foreach ( $this->block_type->uses_context as $context_name ) {
				if ( array_key_exists( $context_name, $this->available_context ) ) {
					$context[ $context_name ] = $this->available_context[ $context_name ];
				}
			}
		}

		return $context;
	}

	/**
	 * List of inner blocks (of this same class).
	 *
	 * Use `$block->inner_blocks` to access this.
	 *
	 * @since 5.6.0
	 * @return WP_Block[]
	 */
	protected function get_inner_blocks() {
		if ( ! empty( $this->parsed_block['innerBlocks'] ) ) {
			$child_context = $this->available_context;

			if ( ! empty( $this->block_type->provides_context ) ) {
				foreach ( $this->block_type->provides_context as $context_name => $attribute_name ) {
					if ( array_key_exists( $attribute_name, $this->attributes ) ) {
						$child_context[ $context_name ] = $this->attributes[ $attribute_name ];
					}
				}
			}

			return new WP_Block_List(
				$this->parsed_block['innerBlocks'],
				$child_context,
				$this->registry
			);
		}

		return array();
	}

	/**
	 * Resultant HTML from inside block comment delimiters after removing inner
	 * blocks.
	 *
	 * Use `$block->inner_html` to access this.
	 *
	 * @example "...Just <!-- wp:test /--> testing..." -> "Just testing..."
	 *
	 * @since 5.6.0
	 * @return string
	 */
	protected function get_inner_html() {
		if ( ! empty( $this->parsed_block['innerHTML'] ) ) {
			return $this->parsed_block['innerHTML'];
		}

		return '';
	}

	/**
	 * List of string fragments and null markers where inner blocks were found
	 *
	 * Use `$block->inner_content` to access this.
	 *
	 * @example array(
	 *   'inner_html'    => 'BeforeInnerAfter',
	 *   'inner_blocks'  => array( block, block ),
	 *   'inner_content' => array( 'Before', null, 'Inner', null, 'After' ),
	 * )
	 *
	 * @since 5.6.0
	 * @return array
	 */
	protected function get_inner_content() {
		if ( ! empty( $this->parsed_block['innerContent'] ) ) {
			return $this->parsed_block['innerContent'];
		}

		return array();
	}

	/**
	 * Generates the render output for the block.
	 *
	 * @since 5.5.0
	 *
	 * @param array $options {
	 *   Optional options object.
	 *
	 *   @type bool $dynamic Defaults to 'true'. Optionally set to false to avoid using the block's render_callback.
	 * }
	 * @return string Rendered block output.
	 */
	public function render( $options = array() ) {
		global $post;

		/** This filter is documented in wp-includes/blocks.php */
		$pre_render = apply_filters( 'pre_render_block', null, $this->parsed_block );
		if ( ! is_null( $pre_render ) ) {
			return $pre_render;
		}

		$options = wp_parse_args(
			$options,
			array(
				'dynamic' => true,
			)
		);

		$initial_parsed_block      = $this->parsed_block;
		$initial_available_context = $this->available_context;
		$initial_cached_properties = $this->cached_properties;

		/**
		 * Filters a block which is to be rendered by render_block() or
		 * WP_Block::render().
		 *
		 * @since 5.1.0
		 *
		 * @param array $parsed_block The block being rendered.
		 * @param array $source_block An un-modified copy of $parsed_block, as it appeared in the source content.
		 */
		$parsed_block = apply_filters(
			'render_block_data',
			$this->parsed_block,
			$initial_parsed_block
		);

		/**
		 * Filters the default context of a block which is to be rendered by
		 * render_block() or WP_Block::render().
		 *
		 * @since 5.5.0
		 *
		 * @param array $available_context Default context.
		 * @param array $parsed_block      Block being rendered, filtered by `render_block_data`.
		 */
		$available_context = apply_filters(
			'render_block_context',
			$this->available_context,
			$this->parsed_block
		);

		$this->reset( $parsed_block, $available_context );

		$is_dynamic = $options['dynamic']
			&& $this->name
			&& null !== $this->block_type
			&& $this->block_type->is_dynamic();

		$block_content = '';

		if ( ! $options['dynamic'] || empty( $this->block_type->skip_inner_blocks ) ) {
			$index = 0;
			foreach ( $this->inner_content as $chunk ) {
				$block_content .= is_string( $chunk ) ?
					$chunk :
					$this->inner_blocks[ $index++ ]->render();
			}
		}

		if ( $is_dynamic ) {
			$global_post = $post;
			$parent      = WP_Block_Supports::$block_to_render;

			WP_Block_Supports::$block_to_render = $this->parsed_block;

			$block_content = (string) call_user_func(
				$this->block_type->render_callback,
				$this->attributes,
				$block_content,
				$this
			);

			WP_Block_Supports::$block_to_render = $parent;

			$post = $global_post;
		}

		if ( ! empty( $this->block_type->script ) ) {
			wp_enqueue_script( $this->block_type->script );
		}

		if ( ! empty( $this->block_type->style ) ) {
			wp_enqueue_style( $this->block_type->style );
		}

		/**
		 * Filters the content of a single block.
		 *
		 * @since 5.0.0
		 *
		 * @param string $block_content The block content about to be appended.
		 * @param array  $block         The full block, including name and attributes.
		 */
		$block_content = apply_filters( 'render_block', $block_content, $this->parsed_block );

		$this->reset(
			$initial_parsed_block,
			$initial_available_context,
			$initial_cached_properties
		);

		return $block_content;
	}

}
