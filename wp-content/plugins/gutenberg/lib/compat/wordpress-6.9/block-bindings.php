<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName // Needed for WP_Block_Context_Extractor helper class.
/**
 * Block Bindings: Support for generically setting rich-text block attributes.
 *
 * @since 6.9.0
 * @package gutenberg
 * @subpackage Block Bindings
 */


// The following filter can be removed once the minimum required WordPress version is 6.9 or newer.
add_filter(
	'block_bindings_supported_attributes',
	function ( $attributes, $block_type ) {
		if ( 'core/image' === $block_type && ! in_array( 'caption', $attributes, true ) ) {
			$attributes[] = 'caption';
		}
		if ( 'core/post-date' === $block_type && ! in_array( 'datetime', $attributes, true ) ) {
			$attributes[] = 'datetime';
		}
		if (
			in_array( $block_type, array( 'core/navigation-link', 'core/navigation-submenu' ), true ) &&
			! in_array( 'url', $attributes, true )
		) {
			$attributes[] = 'url';
		}
		return $attributes;
	},
	10,
	2
);

// The following filter can be removed once the minimum required WordPress version is 6.9 or newer.
add_filter(
	'block_editor_settings_all',
	function ( $editor_settings ) {
		$editor_settings['__experimentalBlockBindingsSupportedAttributes'] = array();
		foreach ( array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() ) as $block_type ) {
			$supported_block_attributes = gutenberg_get_block_bindings_supported_attributes( $block_type );
			if ( ! empty( $supported_block_attributes ) ) {
				$editor_settings['__experimentalBlockBindingsSupportedAttributes'][ $block_type ] = $supported_block_attributes;
			}
		}
		return $editor_settings;
	}
);

/**
 * Callback function for the render_block filter.
 *
 * @since 6.9.0
 *
 * @param string   $block_content The block content.
 * @param array    $block         The full block, including name and attributes.
 * @param WP_Block $instance      The block instance.
 */
function gutenberg_block_bindings_render_block( $block_content, $block, $instance ) {
	static $inside_block_bindings_render = false;
	if ( $inside_block_bindings_render ) {
		return $block_content;
	}

	// Process the block bindings and get attributes updated with the values from the sources.
	$computed_attributes = gutenberg_process_block_bindings( $instance );
	if ( empty( $computed_attributes ) ) {
		return $block_content;
	}

	/*
	 * Merge the computed attributes with the original attributes.
	 *
	 * Note that this is not a recursive merge, meaning that nested attributes --
	 * such as block bindings metadata -- will be completely replaced.
	 * This is desirable. At this point, Core has already processed any block
	 * bindings that it supports. What remains to be processed are only the attributes
	 * for which support was added later (through the `block_bindings_supported_attributes`
	 * filter). To do so, we'll run `$instance->render()` once more
	 * so the block can update its content based on those attributes.
	 */
	$instance->attributes = array_merge( $instance->attributes, $computed_attributes );

	/*
	 * If we're dealing with the Button block, we remove the bindings metadata
	 * in order to avoid having it reprocessed, which would lead to Core
	 * capitalizing the wrapper tag (e.g. <DIV>).
	 */
	if ( 'core/button' === $instance->name ) {
		unset( $instance->parsed_block['attrs']['metadata']['bindings'] );
	}

	/**
	 * This filter (`gutenberg_block_bindings_render_block`) is called from `WP_Block::render()`.
	 * To avoid infinite recursion, we set a flag that this filter checks when invoked which tells
	 * it to exit early.
	 */
	$inside_block_bindings_render = true;
	$block_content                = $instance->render();
	$inside_block_bindings_render = false;

	if ( ! empty( $computed_attributes ) && ! empty( $block_content ) ) {
		foreach ( $computed_attributes as $attribute_name => $source_value ) {
			$block_content = gutenberg_replace_html( $block_content, $attribute_name, $source_value, $instance->block_type );
		}
	}

	return $block_content;
}
add_filter( 'render_block', 'gutenberg_block_bindings_render_block', 10, 3 );

/**
 * Retrieves the list of block attributes supported by block bindings.
 *
 * @since 6.9.0
 *
 * @param string $block_type The block type whose supported attributes are being retrieved.
 * @return array The list of block attributes that are supported by block bindings.
 */
function gutenberg_get_block_bindings_supported_attributes( $block_type ) {
	/*
	 * List of block attributes supported by Block Bindings in WP 6.8.
	 * DO NOT MODIFY THIS ARRAY. It's a snapshot of what Core supports in 6.8.
	 * Use the `block_bindings_supported_attributes` filter instead to add support
	 * for new block attributes.
	 */
	$block_bindings_supported_attributes_6_8 = array(
		'core/paragraph' => array( 'content' ),
		'core/heading'   => array( 'content' ),
		'core/image'     => array( 'id', 'url', 'title', 'alt' ),
		'core/button'    => array( 'url', 'text', 'linkTarget', 'rel' ),
	);

	$supported_block_attributes =
		isset( $block_type, $block_bindings_supported_attributes_6_8[ $block_type ] ) ?
			$block_bindings_supported_attributes_6_8[ $block_type ] :
			array();

	/**
	 * Filters the supported block attributes for block bindings.
	 *
	 * @since 6.9.0
	 *
	 * @param string[] $supported_block_attributes The block's attributes that are supported by block bindings.
	 * @param string   $block_type                 The block type whose attributes are being filtered.
	 */
	$supported_block_attributes = apply_filters(
		'block_bindings_supported_attributes',
		$supported_block_attributes,
		$block_type
	);

	/**
	 * Filters the supported block attributes for block bindings.
	 *
	 * The dynamic portion of the hook name, `$block_type`, refers to the block type
	 * whose attributes are being filtered.
	 *
	 * @since 6.9.0
	 *
	 * @param string[] $supported_block_attributes The block's attributes that are supported by block bindings.
	 */
	$supported_block_attributes = apply_filters(
		"block_bindings_supported_attributes_{$block_type}",
		$supported_block_attributes
	);

	return $supported_block_attributes;
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
 * @since 6.9.0
 *
 * @param WP_Block $instance The block instance.
 * @return array The computed block attributes for the provided block bindings.
 */
function gutenberg_process_block_bindings( $instance ) {
	$block_type          = $instance->name;
	$parsed_block        = $instance->parsed_block;
	$computed_attributes = array();

	/*
	 * List of block attributes supported by Block Bindings in WP 6.8.
	 * DO NOT MODIFY THIS ARRAY. It's a snapshot of what Core supports in 6.8.
	 * Use the `block_bindings_supported_attributes` filter instead to add support
	 * for new block attributes.
	 */
	$block_bindings_supported_attributes_6_8 = array(
		'core/paragraph' => array( 'content' ),
		'core/heading'   => array( 'content' ),
		'core/image'     => array( 'id', 'url', 'title', 'alt' ),
		'core/button'    => array( 'url', 'text', 'linkTarget', 'rel' ),
	);

	$supported_block_attributes = gutenberg_get_block_bindings_supported_attributes( $block_type );

	/*
	 * Remove attributes that we know are processed by WP 6.8 from the list,
	 * except if we're dealing with the button block, since WP 6.8 capitalizes its
	 * tag name (e.g. <DIV>).
	 */
	if ( 'core/button' !== $block_type && isset( $block_type, $block_bindings_supported_attributes_6_8[ $block_type ] ) ) {
		$supported_block_attributes = array_diff(
			$supported_block_attributes,
			$block_bindings_supported_attributes_6_8[ $block_type ]
		);
	}

	// If the block doesn't have the bindings property, isn't one of the supported
	// block types, or the bindings property is not an array, return the block content.
	if (
		empty( $supported_block_attributes ) ||
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
		foreach ( $supported_block_attributes as $attribute_name ) {
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
		if ( ! in_array( $attribute_name, $supported_block_attributes, true ) ) {
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

		if ( ! class_exists( 'WP_Block_Context_Extractor' ) ) {
			// phpcs:ignore Gutenberg.Commenting.SinceTag.MissingClassSinceTag
			class WP_Block_Context_Extractor extends WP_Block {
				/**
				 * Static methods of subclasses have access to protected properties
				 * of instances of the parent class.
				 * In this case, this gives us access to `available_context`.
				 */
				// phpcs:ignore Gutenberg.Commenting.SinceTag.MissingMethodSinceTag
				public static function get_available_context( $instance ) {
					return $instance->available_context;
				}
			}
		}
		$available_context = WP_Block_Context_Extractor::get_available_context( $instance );

		// Adds the necessary context defined by the source.
		if ( ! empty( $block_binding_source->uses_context ) ) {
			foreach ( $block_binding_source->uses_context as $context_name ) {
				if ( array_key_exists( $context_name, $available_context ) ) {
					$instance->context[ $context_name ] = $available_context[ $context_name ];
				}
			}
		}

		$source_args  = ! empty( $block_binding['args'] ) && is_array( $block_binding['args'] ) ? $block_binding['args'] : array();
		$source_value = $block_binding_source->get_value( $source_args, $instance, $attribute_name );

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
 * @param WP_Block_Type $block_type     The block type.
 * @return string The modified block content.
 */
function gutenberg_replace_html( string $block_content, string $attribute_name, $source_value, WP_Block_Type $block_type ) {
	if ( ! isset( $block_type->attributes[ $attribute_name ]['source'] ) ) {
		return $block_content;
	}

	// Depending on the attribute source, the processing will be different.
	switch ( $block_type->attributes[ $attribute_name ]['source'] ) {
		case 'html':
		case 'rich-text':
			$block_reader = gutenberg_get_block_bindings_processor( $block_content );

			// TODO: Support for CSS selectors whenever they are ready in the HTML API.
			// In the meantime, support comma-separated selectors by exploding them into an array.
			$selectors = explode( ',', $block_type->attributes[ $attribute_name ]['selector'] );
			// Add a bookmark to the first tag to be able to iterate over the selectors.
			$block_reader->next_tag();
			$block_reader->set_bookmark( 'iterate-selectors' );

			foreach ( $selectors as $selector ) {
				// If the parent tag, or any of its children, matches the selector, replace the HTML.
				if ( strcasecmp( $block_reader->get_tag(), $selector ) === 0 || $block_reader->next_tag(
					array(
						'tag_name' => $selector,
					)
				) ) {
					// TODO: Use `WP_HTML_Processor::set_inner_html` method once it's available.
					$block_reader->release_bookmark( 'iterate-selectors' );
					$block_reader->replace_rich_text( wp_kses_post( $source_value ) );
					return $block_reader->get_updated_html();
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

function gutenberg_get_block_bindings_processor( string $block_content ) {
	$internal_processor_class = new class('', WP_HTML_Processor::CONSTRUCTOR_UNLOCK_CODE) extends WP_HTML_Processor {
		/**
		 * Replace the rich text content between a tag opener and matching closer.
		 *
		 * When stopped on a tag opener, replace the content enclosed by it and its
		 * matching closer with the provided rich text.
		 *
		 * @param string $rich_text The rich text to replace the original content with.
		 * @return bool True on success.
		 */
		// phpcs:ignore Gutenberg.CodeAnalysis.GuardedFunctionAndClassNames.FunctionNotGuardedAgainstRedeclaration
		public function replace_rich_text( $rich_text ) {
			if ( $this->is_tag_closer() || ! $this->expects_closer() ) {
				return false;
			}

			$depth = $this->get_current_depth();

			$this->set_bookmark( '_wp_block_bindings_tag_opener' );
			// The bookmark names are prefixed with `_` so the key below has an extra `_`.
			$tag_opener = $this->bookmarks['__wp_block_bindings_tag_opener'];
			$start      = $tag_opener->start + $tag_opener->length;
			$this->release_bookmark( '_wp_block_bindings_tag_opener' );

			// Find matching tag closer.
			while ( $this->next_token() && $this->get_current_depth() >= $depth ) {
			}

			$this->set_bookmark( '_wp_block_bindings_tag_closer' );
			$tag_closer = $this->bookmarks['__wp_block_bindings_tag_closer'];
			$end        = $tag_closer->start;
			$this->release_bookmark( '_wp_block_bindings_tag_closer' );

			$this->lexical_updates[] = new WP_HTML_Text_Replacement(
				$start,
				$end - $start,
				$rich_text
			);

			return true;
		}
	};

	return $internal_processor_class::create_fragment( $block_content );
}
