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
	 * Block context values.
	 *
	 * @since 5.5.0
	 * @var array
	 */
	public $context = array();

	/**
	 * All available context of the current hierarchy.
	 *
	 * @since 5.5.0
	 * @var array
	 * @access protected
	 */
	protected $available_context;

	/**
	 * Block type registry.
	 *
	 * @since 5.9.0
	 * @var WP_Block_Type_Registry
	 * @access protected
	 */
	protected $registry;

	/**
	 * List of inner blocks (of this same class)
	 *
	 * @since 5.5.0
	 * @var WP_Block_List
	 */
	public $inner_blocks = array();

	/**
	 * Resultant HTML from inside block comment delimiters after removing inner
	 * blocks.
	 *
	 * @example "...Just <!-- wp:test /--> testing..." -> "Just testing..."
	 *
	 * @since 5.5.0
	 * @var string
	 */
	public $inner_html = '';

	/**
	 * List of string fragments and null markers where inner blocks were found
	 *
	 * @example array(
	 *   'inner_html'    => 'BeforeInnerAfter',
	 *   'inner_blocks'  => array( block, block ),
	 *   'inner_content' => array( 'Before', null, 'Inner', null, 'After' ),
	 * )
	 *
	 * @since 5.5.0
	 * @var array
	 */
	public $inner_content = array();

	/**
	 * Constructor.
	 *
	 * Populates object properties from the provided block instance argument.
	 *
	 * The given array of context values will not necessarily be available on
	 * the instance itself, but is treated as the full set of values provided by
	 * the block's ancestry. This is assigned to the private `available_context`
	 * property. Only values which are configured to consumed by the block via
	 * its registered type will be assigned to the block's `context` property.
	 *
	 * @since 5.5.0
	 *
	 * @param array                  $block             Array of parsed block properties.
	 * @param array                  $available_context Optional array of ancestry context values.
	 * @param WP_Block_Type_Registry $registry          Optional block type registry.
	 */
	public function __construct( $block, $available_context = array(), $registry = null ) {
		$this->parsed_block = $block;
		$this->name         = $block['blockName'];

		if ( is_null( $registry ) ) {
			$registry = WP_Block_Type_Registry::get_instance();
		}

		$this->registry = $registry;

		$this->block_type = $registry->get_registered( $this->name );

		$this->available_context = $available_context;

		if ( ! empty( $this->block_type->uses_context ) ) {
			foreach ( $this->block_type->uses_context as $context_name ) {
				if ( array_key_exists( $context_name, $this->available_context ) ) {
					$this->context[ $context_name ] = $this->available_context[ $context_name ];
				}
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$child_context = $this->available_context;

			if ( ! empty( $this->block_type->provides_context ) ) {
				foreach ( $this->block_type->provides_context as $context_name => $attribute_name ) {
					if ( array_key_exists( $attribute_name, $this->attributes ) ) {
						$child_context[ $context_name ] = $this->attributes[ $attribute_name ];
					}
				}
			}

			$this->inner_blocks = new WP_Block_List( $block['innerBlocks'], $child_context, $registry );
		}

		if ( ! empty( $block['innerHTML'] ) ) {
			$this->inner_html = $block['innerHTML'];
		}

		if ( ! empty( $block['innerContent'] ) ) {
			$this->inner_content = $block['innerContent'];
		}
	}

	/**
	 * Returns a value from an inaccessible property.
	 *
	 * This is used to lazily initialize the `attributes` property of a block,
	 * such that it is only prepared with default attributes at the time that
	 * the property is accessed. For all other inaccessible properties, a `null`
	 * value is returned.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name Property name.
	 * @return array|null Prepared attributes, or null.
	 */
	public function __get( $name ) {
		if ( 'attributes' === $name ) {
			$this->attributes = isset( $this->parsed_block['attrs'] ) ?
				$this->parsed_block['attrs'] :
				array();

			if ( ! is_null( $this->block_type ) ) {
				$this->attributes = $this->block_type->prepare_attributes_for_render( $this->attributes );
			}

			return $this->attributes;
		}

		return null;
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
		$options = wp_parse_args(
			$options,
			array(
				'dynamic' => true,
			)
		);

		$is_dynamic    = $options['dynamic'] && $this->name && null !== $this->block_type && $this->block_type->is_dynamic();
		$block_content = '';

		if ( ! $options['dynamic'] || empty( $this->block_type->skip_inner_blocks ) ) {
			$index = 0;

			foreach ( $this->inner_content as $chunk ) {
				if ( is_string( $chunk ) ) {
					$block_content .= $chunk;
				} else {
					$inner_block  = $this->inner_blocks[ $index ];
					$parent_block = $this;

					/** This filter is documented in wp-includes/blocks.php */
					$pre_render = apply_filters( 'pre_render_block', null, $inner_block->parsed_block, $parent_block );

					if ( ! is_null( $pre_render ) ) {
						$block_content .= $pre_render;
					} else {
						$source_block = $inner_block->parsed_block;

						/** This filter is documented in wp-includes/blocks.php */
						$inner_block->parsed_block = apply_filters( 'render_block_data', $inner_block->parsed_block, $source_block, $parent_block );

						/** This filter is documented in wp-includes/blocks.php */
						$inner_block->context = apply_filters( 'render_block_context', $inner_block->context, $inner_block->parsed_block, $parent_block );

						$block_content .= $inner_block->render();
					}

					$index++;
				}
			}
		}

		if ( $is_dynamic ) {
			$global_post = $post;
			$parent      = WP_Block_Supports::$block_to_render;

			WP_Block_Supports::$block_to_render = $this->parsed_block;

			$block_content = (string) call_user_func( $this->block_type->render_callback, $this->attributes, $block_content, $this );

			WP_Block_Supports::$block_to_render = $parent;

			$post = $global_post;
		}

		if ( ! empty( $this->block_type->script ) ) {
			wp_enqueue_script( $this->block_type->script );
		}

		if ( ! empty( $this->block_type->view_script ) && empty( $this->block_type->render_callback ) ) {
			wp_enqueue_script( $this->block_type->view_script );
		}

		if ( ! empty( $this->block_type->style ) ) {
			wp_enqueue_style( $this->block_type->style );
		}

		/**
		 * Filters the content of a single block.
		 *
		 * @since 5.0.0
		 * @since 5.9.0 The `$instance` parameter was added.
		 *
		 * @param string   $block_content The block content about to be appended.
		 * @param array    $block         The full block, including name and attributes.
		 * @param WP_Block $instance      The block instance.
		 */
		$block_content = apply_filters( 'render_block', $block_content, $this->parsed_block, $this );

		/**
		 * Filters the content of a single block.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to
		 * the block name, e.g. "core/paragraph".
		 *
		 * @since 5.7.0
		 * @since 5.9.0 The `$instance` parameter was added.
		 *
		 * @param string   $block_content The block content about to be appended.
		 * @param array    $block         The full block, including name and attributes.
		 * @param WP_Block $instance      The block instance.
		 */
		$block_content = apply_filters( "render_block_{$this->name}", $block_content, $this->parsed_block, $this );

		return $block_content;
	}

}
