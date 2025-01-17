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
#[AllowDynamicProperties]
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
	protected $available_context = array();

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
	 * @param array                  $block             {
	 *     An associative array of a single parsed block object. See WP_Block_Parser_Block.
	 *
	 *     @type string   $blockName    Name of block.
	 *     @type array    $attrs        Attributes from block comment delimiters.
	 *     @type array    $innerBlocks  List of inner blocks. An array of arrays that
	 *                                  have the same structure as this one.
	 *     @type string   $innerHTML    HTML from inside block comment delimiters.
	 *     @type array    $innerContent List of string fragments and null markers where inner blocks were found.
	 * }
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

		$this->refresh_context_dependents();
	}

	/**
	 * Updates the context for the current block and its inner blocks.
	 *
	 * The method updates the context of inner blocks, if any, by passing down
	 * any context values the block provides (`provides_context`).
	 *
	 * If the block has inner blocks, the method recursively processes them by creating new instances of `WP_Block`
	 * for each inner block and updating their context based on the block's `provides_context` property.
	 *
	 * @since 6.8.0
	 */
	public function refresh_context_dependents() {
		/*
		 * Merging the `$context` property here is not ideal, but for now needs to happen because of backward compatibility.
		 * Ideally, the `$context` property itself would not be filterable directly and only the `$available_context` would be filterable.
		 * However, this needs to be separately explored whether it's possible without breakage.
		 */
		$this->available_context = array_merge( $this->available_context, $this->context );

		if ( ! empty( $this->block_type->uses_context ) ) {
			foreach ( $this->block_type->uses_context as $context_name ) {
				if ( array_key_exists( $context_name, $this->available_context ) ) {
					$this->context[ $context_name ] = $this->available_context[ $context_name ];
				}
			}
		}

		$this->refresh_parsed_block_dependents();
	}

	/**
	 * Updates the parsed block content for the current block and its inner blocks.
	 *
	 * This method sets the `inner_html` and `inner_content` properties of the block based on the parsed
	 * block content provided during initialization. It ensures that the block instance reflects the
	 * most up-to-date content for both the inner HTML and any string fragments around inner blocks.
	 *
	 * If the block has inner blocks, this method initializes a new `WP_Block_List` for them, ensuring the
	 * correct content and context are updated for each nested block.
	 *
	 * @since 6.8.0
	 */
	public function refresh_parsed_block_dependents() {
		if ( ! empty( $this->parsed_block['innerBlocks'] ) ) {
			$child_context = $this->available_context;

			if ( ! empty( $this->block_type->provides_context ) ) {
				foreach ( $this->block_type->provides_context as $context_name => $attribute_name ) {
					if ( array_key_exists( $attribute_name, $this->attributes ) ) {
						$child_context[ $context_name ] = $this->attributes[ $attribute_name ];
					}
				}
			}

			$this->inner_blocks = new WP_Block_List( $this->parsed_block['innerBlocks'], $child_context, $this->registry );
		}

		if ( ! empty( $this->parsed_block['innerHTML'] ) ) {
			$this->inner_html = $this->parsed_block['innerHTML'];
		}

		if ( ! empty( $this->parsed_block['innerContent'] ) ) {
			$this->inner_content = $this->parsed_block['innerContent'];
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
	 * Processes the block bindings and updates the block attributes with the values from the sources.
	 *
	 * A block might contain bindings in its attributes. Bindings are mappings
	 * between an attribute of the block and a source. A "source" is a function
	 * registered with `register_block_bindings_source()` that defines how to
	 * retrieve a value from outside the block, e.g. from post meta.
	 *
	 * This function will process those bindings and update the block's attributes
	 * with the values coming from the bindings.
	 *
	 * ### Example
	 *
	 * The "bindings" property for an Image block might look like this:
	 *
	 * ```json
	 * {
	 *   "metadata": {
	 *     "bindings": {
	 *       "title": {
	 *         "source": "core/post-meta",
	 *         "args": { "key": "text_custom_field" }
	 *       },
	 *       "url": {
	 *         "source": "core/post-meta",
	 *         "args": { "key": "url_custom_field" }
	 *       }
	 *     }
	 *   }
	 * }
	 * ```
	 *
	 * The above example will replace the `title` and `url` attributes of the Image
	 * block with the values of the `text_custom_field` and `url_custom_field` post meta.
	 *
	 * @since 6.5.0
	 * @since 6.6.0 Handle the `__default` attribute for pattern overrides.
	 * @since 6.7.0 Return any updated bindings metadata in the computed attributes.
	 *
	 * @return array The computed block attributes for the provided block bindings.
	 */
	private function process_block_bindings() {
		$parsed_block               = $this->parsed_block;
		$computed_attributes        = array();
		$supported_block_attributes = array(
			'core/paragraph' => array( 'content' ),
			'core/heading'   => array( 'content' ),
			'core/image'     => array( 'id', 'url', 'title', 'alt' ),
			'core/button'    => array( 'url', 'text', 'linkTarget', 'rel' ),
		);

		// If the block doesn't have the bindings property, isn't one of the supported
		// block types, or the bindings property is not an array, return the block content.
		if (
			! isset( $supported_block_attributes[ $this->name ] ) ||
			empty( $parsed_block['attrs']['metadata']['bindings'] ) ||
			! is_array( $parsed_block['attrs']['metadata']['bindings'] )
		) {
			return $computed_attributes;
		}

		$bindings = $parsed_block['attrs']['metadata']['bindings'];

		/*
		 * If the default binding is set for pattern overrides, replace it
		 * with a pattern override binding for all supported attributes.
		 */
		if (
			isset( $bindings['__default']['source'] ) &&
			'core/pattern-overrides' === $bindings['__default']['source']
		) {
			$updated_bindings = array();

			/*
			 * Build a binding array of all supported attributes.
			 * Note that this also omits the `__default` attribute from the
			 * resulting array.
			 */
			foreach ( $supported_block_attributes[ $parsed_block['blockName'] ] as $attribute_name ) {
				// Retain any non-pattern override bindings that might be present.
				$updated_bindings[ $attribute_name ] = isset( $bindings[ $attribute_name ] )
					? $bindings[ $attribute_name ]
					: array( 'source' => 'core/pattern-overrides' );
			}
			$bindings = $updated_bindings;
			/*
			 * Update the bindings metadata of the computed attributes.
			 * This ensures the block receives the expanded __default binding metadata when it renders.
			 */
			$computed_attributes['metadata'] = array_merge(
				$parsed_block['attrs']['metadata'],
				array( 'bindings' => $bindings )
			);
		}

		foreach ( $bindings as $attribute_name => $block_binding ) {
			// If the attribute is not in the supported list, process next attribute.
			if ( ! in_array( $attribute_name, $supported_block_attributes[ $this->name ], true ) ) {
				continue;
			}
			// If no source is provided, or that source is not registered, process next attribute.
			if ( ! isset( $block_binding['source'] ) || ! is_string( $block_binding['source'] ) ) {
				continue;
			}

			$block_binding_source = get_block_bindings_source( $block_binding['source'] );
			if ( null === $block_binding_source ) {
				continue;
			}

			// Adds the necessary context defined by the source.
			if ( ! empty( $block_binding_source->uses_context ) ) {
				foreach ( $block_binding_source->uses_context as $context_name ) {
					if ( array_key_exists( $context_name, $this->available_context ) ) {
						$this->context[ $context_name ] = $this->available_context[ $context_name ];
					}
				}
			}

			$source_args  = ! empty( $block_binding['args'] ) && is_array( $block_binding['args'] ) ? $block_binding['args'] : array();
			$source_value = $block_binding_source->get_value( $source_args, $this, $attribute_name );

			// If the value is not null, process the HTML based on the block and the attribute.
			if ( ! is_null( $source_value ) ) {
				$computed_attributes[ $attribute_name ] = $source_value;
			}
		}

		return $computed_attributes;
	}

	/**
	 * Depending on the block attribute name, replace its value in the HTML based on the value provided.
	 *
	 * @since 6.5.0
	 *
	 * @param string $block_content  Block content.
	 * @param string $attribute_name The attribute name to replace.
	 * @param mixed  $source_value   The value used to replace in the HTML.
	 * @return string The modified block content.
	 */
	private function replace_html( string $block_content, string $attribute_name, $source_value ) {
		$block_type = $this->block_type;
		if ( ! isset( $block_type->attributes[ $attribute_name ]['source'] ) ) {
			return $block_content;
		}

		// Depending on the attribute source, the processing will be different.
		switch ( $block_type->attributes[ $attribute_name ]['source'] ) {
			case 'html':
			case 'rich-text':
				$block_reader = new WP_HTML_Tag_Processor( $block_content );

				// TODO: Support for CSS selectors whenever they are ready in the HTML API.
				// In the meantime, support comma-separated selectors by exploding them into an array.
				$selectors = explode( ',', $block_type->attributes[ $attribute_name ]['selector'] );
				// Add a bookmark to the first tag to be able to iterate over the selectors.
				$block_reader->next_tag();
				$block_reader->set_bookmark( 'iterate-selectors' );

				// TODO: This shouldn't be needed when the `set_inner_html` function is ready.
				// Store the parent tag and its attributes to be able to restore them later in the button.
				// The button block has a wrapper while the paragraph and heading blocks don't.
				if ( 'core/button' === $this->name ) {
					$button_wrapper                 = $block_reader->get_tag();
					$button_wrapper_attribute_names = $block_reader->get_attribute_names_with_prefix( '' );
					$button_wrapper_attrs           = array();
					foreach ( $button_wrapper_attribute_names as $name ) {
						$button_wrapper_attrs[ $name ] = $block_reader->get_attribute( $name );
					}
				}

				foreach ( $selectors as $selector ) {
					// If the parent tag, or any of its children, matches the selector, replace the HTML.
					if ( strcasecmp( $block_reader->get_tag(), $selector ) === 0 || $block_reader->next_tag(
						array(
							'tag_name' => $selector,
						)
					) ) {
						$block_reader->release_bookmark( 'iterate-selectors' );

						// TODO: Use `set_inner_html` method whenever it's ready in the HTML API.
						// Until then, it is hardcoded for the paragraph, heading, and button blocks.
						// Store the tag and its attributes to be able to restore them later.
						$selector_attribute_names = $block_reader->get_attribute_names_with_prefix( '' );
						$selector_attrs           = array();
						foreach ( $selector_attribute_names as $name ) {
							$selector_attrs[ $name ] = $block_reader->get_attribute( $name );
						}
						$selector_markup = "<$selector>" . wp_kses_post( $source_value ) . "</$selector>";
						$amended_content = new WP_HTML_Tag_Processor( $selector_markup );
						$amended_content->next_tag();
						foreach ( $selector_attrs as $attribute_key => $attribute_value ) {
							$amended_content->set_attribute( $attribute_key, $attribute_value );
						}
						if ( 'core/paragraph' === $this->name || 'core/heading' === $this->name ) {
							return $amended_content->get_updated_html();
						}
						if ( 'core/button' === $this->name ) {
							$button_markup  = "<$button_wrapper>{$amended_content->get_updated_html()}</$button_wrapper>";
							$amended_button = new WP_HTML_Tag_Processor( $button_markup );
							$amended_button->next_tag();
							foreach ( $button_wrapper_attrs as $attribute_key => $attribute_value ) {
								$amended_button->set_attribute( $attribute_key, $attribute_value );
							}
							return $amended_button->get_updated_html();
						}
					} else {
						$block_reader->seek( 'iterate-selectors' );
					}
				}
				$block_reader->release_bookmark( 'iterate-selectors' );
				return $block_content;

			case 'attribute':
				$amended_content = new WP_HTML_Tag_Processor( $block_content );
				if ( ! $amended_content->next_tag(
					array(
						// TODO: build the query from CSS selector.
						'tag_name' => $block_type->attributes[ $attribute_name ]['selector'],
					)
				) ) {
					return $block_content;
				}
				$amended_content->set_attribute( $block_type->attributes[ $attribute_name ]['attribute'], $source_value );
				return $amended_content->get_updated_html();

			default:
				return $block_content;
		}
	}


	/**
	 * Generates the render output for the block.
	 *
	 * @since 5.5.0
	 * @since 6.5.0 Added block bindings processing.
	 *
	 * @global WP_Post $post Global post object.
	 *
	 * @param array $options {
	 *     Optional options object.
	 *
	 *     @type bool $dynamic Defaults to 'true'. Optionally set to false to avoid using the block's render_callback.
	 * }
	 * @return string Rendered block output.
	 */
	public function render( $options = array() ) {
		global $post;

		/*
		 * There can be only one root interactive block at a time because the rendered HTML of that block contains
		 * the rendered HTML of all its inner blocks, including any interactive block.
		 */
		static $root_interactive_block = null;
		/**
		 * Filters whether Interactivity API should process directives.
		 *
		 * @since 6.6.0
		 *
		 * @param bool $enabled Whether the directives processing is enabled.
		 */
		$interactivity_process_directives_enabled = apply_filters( 'interactivity_process_directives', true );
		if (
			$interactivity_process_directives_enabled && null === $root_interactive_block && (
				( isset( $this->block_type->supports['interactivity'] ) && true === $this->block_type->supports['interactivity'] ) ||
				! empty( $this->block_type->supports['interactivity']['interactive'] )
			)
		) {
			$root_interactive_block = $this;
		}

		$options = wp_parse_args(
			$options,
			array(
				'dynamic' => true,
			)
		);

		// Process the block bindings and get attributes updated with the values from the sources.
		$computed_attributes = $this->process_block_bindings();
		if ( ! empty( $computed_attributes ) ) {
			// Merge the computed attributes with the original attributes.
			$this->attributes = array_merge( $this->attributes, $computed_attributes );
		}

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
						$source_block        = $inner_block->parsed_block;
						$inner_block_context = $inner_block->context;

						/** This filter is documented in wp-includes/blocks.php */
						$inner_block->parsed_block = apply_filters( 'render_block_data', $inner_block->parsed_block, $source_block, $parent_block );

						/** This filter is documented in wp-includes/blocks.php */
						$inner_block->context = apply_filters( 'render_block_context', $inner_block->context, $inner_block->parsed_block, $parent_block );

						/*
						 * The `refresh_context_dependents()` method already calls `refresh_parsed_block_dependents()`.
						 * Therefore the second condition is irrelevant if the first one is satisfied.
						 */
						if ( $inner_block->context !== $inner_block_context ) {
							$inner_block->refresh_context_dependents();
						} elseif ( $inner_block->parsed_block !== $source_block ) {
							$inner_block->refresh_parsed_block_dependents();
						}

						$block_content .= $inner_block->render();
					}

					++$index;
				}
			}
		}

		if ( ! empty( $computed_attributes ) && ! empty( $block_content ) ) {
			foreach ( $computed_attributes as $attribute_name => $source_value ) {
				$block_content = $this->replace_html( $block_content, $attribute_name, $source_value );
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

		if ( ( ! empty( $this->block_type->script_handles ) ) ) {
			foreach ( $this->block_type->script_handles as $script_handle ) {
				wp_enqueue_script( $script_handle );
			}
		}

		if ( ! empty( $this->block_type->view_script_handles ) ) {
			foreach ( $this->block_type->view_script_handles as $view_script_handle ) {
				wp_enqueue_script( $view_script_handle );
			}
		}

		if ( ! empty( $this->block_type->view_script_module_ids ) ) {
			foreach ( $this->block_type->view_script_module_ids as $view_script_module_id ) {
				wp_enqueue_script_module( $view_script_module_id );
			}
		}

		if ( ( ! empty( $this->block_type->style_handles ) ) ) {
			foreach ( $this->block_type->style_handles as $style_handle ) {
				wp_enqueue_style( $style_handle );
			}
		}

		if ( ( ! empty( $this->block_type->view_style_handles ) ) ) {
			foreach ( $this->block_type->view_style_handles as $view_style_handle ) {
				wp_enqueue_style( $view_style_handle );
			}
		}

		/**
		 * Filters the content of a single block.
		 *
		 * @since 5.0.0
		 * @since 5.9.0 The `$instance` parameter was added.
		 *
		 * @param string   $block_content The block content.
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
		 * @param string   $block_content The block content.
		 * @param array    $block         The full block, including name and attributes.
		 * @param WP_Block $instance      The block instance.
		 */
		$block_content = apply_filters( "render_block_{$this->name}", $block_content, $this->parsed_block, $this );

		if ( $root_interactive_block === $this ) {
			// The root interactive block has finished rendering. Time to process directives.
			$block_content          = wp_interactivity_process_directives( $block_content );
			$root_interactive_block = null;
		}

		return $block_content;
	}
}
