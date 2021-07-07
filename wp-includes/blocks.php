<?php
/**
 * Functions related to registering and parsing blocks.
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 5.0.0
 */

/**
 * Removes the block asset's path prefix if provided.
 *
 * @since 5.5.0
 *
 * @param string $asset_handle_or_path Asset handle or prefixed path.
 * @return string Path without the prefix or the original value.
 */
function remove_block_asset_path_prefix( $asset_handle_or_path ) {
	$path_prefix = 'file:';
	if ( 0 !== strpos( $asset_handle_or_path, $path_prefix ) ) {
		return $asset_handle_or_path;
	}
	return substr(
		$asset_handle_or_path,
		strlen( $path_prefix )
	);
}

/**
 * Generates the name for an asset based on the name of the block
 * and the field name provided.
 *
 * @since 5.5.0
 *
 * @param string $block_name Name of the block.
 * @param string $field_name Name of the metadata field.
 * @return string Generated asset name for the block's field.
 */
function generate_block_asset_handle( $block_name, $field_name ) {
	if ( 0 === strpos( $block_name, 'core/' ) ) {
		$asset_handle = str_replace( 'core/', 'wp-block-', $block_name );
		if ( 0 === strpos( $field_name, 'editor' ) ) {
			$asset_handle .= '-editor';
		}
		return $asset_handle;
	}

	$field_mappings = array(
		'editorScript' => 'editor-script',
		'script'       => 'script',
		'editorStyle'  => 'editor-style',
		'style'        => 'style',
	);
	return str_replace( '/', '-', $block_name ) .
		'-' . $field_mappings[ $field_name ];
}

/**
 * Finds a script handle for the selected block metadata field. It detects
 * when a path to file was provided and finds a corresponding asset file
 * with details necessary to register the script under automatically
 * generated handle name. It returns unprocessed script handle otherwise.
 *
 * @since 5.5.0
 *
 * @param array  $metadata   Block metadata.
 * @param string $field_name Field name to pick from metadata.
 * @return string|false Script handle provided directly or created through
 *                      script's registration, or false on failure.
 */
function register_block_script_handle( $metadata, $field_name ) {
	if ( empty( $metadata[ $field_name ] ) ) {
		return false;
	}
	$script_handle = $metadata[ $field_name ];
	$script_path   = remove_block_asset_path_prefix( $metadata[ $field_name ] );
	if ( $script_handle === $script_path ) {
		return $script_handle;
	}

	$script_handle     = generate_block_asset_handle( $metadata['name'], $field_name );
	$script_asset_path = realpath(
		dirname( $metadata['file'] ) . '/' .
		substr_replace( $script_path, '.asset.php', - strlen( '.js' ) )
	);
	if ( ! file_exists( $script_asset_path ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: Field name, 2: Block name. */
				__( 'The asset file for the "%1$s" defined in "%2$s" block definition is missing.' ),
				$field_name,
				$metadata['name']
			),
			'5.5.0'
		);
		return false;
	}
	$script_asset = require $script_asset_path;
	$result       = wp_register_script(
		$script_handle,
		plugins_url( $script_path, $metadata['file'] ),
		$script_asset['dependencies'],
		$script_asset['version']
	);
	if ( ! $result ) {
		return false;
	}

	if ( ! empty( $metadata['textdomain'] ) ) {
		wp_set_script_translations( $script_handle, $metadata['textdomain'] );
	}

	return $script_handle;
}

/**
 * Finds a style handle for the block metadata field. It detects when a path
 * to file was provided and registers the style under automatically
 * generated handle name. It returns unprocessed style handle otherwise.
 *
 * @since 5.5.0
 *
 * @param array  $metadata   Block metadata.
 * @param string $field_name Field name to pick from metadata.
 * @return string|false Style handle provided directly or created through
 *                      style's registration, or false on failure.
 */
function register_block_style_handle( $metadata, $field_name ) {
	if ( empty( $metadata[ $field_name ] ) ) {
		return false;
	}
	$is_core_block = isset( $metadata['file'] ) && 0 === strpos( $metadata['file'], ABSPATH . WPINC );
	if ( $is_core_block && ! wp_should_load_separate_core_block_assets() ) {
		return false;
	}

	// Check whether styles should have a ".min" suffix or not.
	$suffix = SCRIPT_DEBUG ? '' : '.min';

	$style_handle = $metadata[ $field_name ];
	$style_path   = remove_block_asset_path_prefix( $metadata[ $field_name ] );

	if ( $style_handle === $style_path && ! $is_core_block ) {
		return $style_handle;
	}

	$style_uri = plugins_url( $style_path, $metadata['file'] );
	if ( $is_core_block ) {
		$style_path = "style$suffix.css";
		$style_uri  = includes_url( 'blocks/' . str_replace( 'core/', '', $metadata['name'] ) . "/style$suffix.css" );
	}

	$style_handle   = generate_block_asset_handle( $metadata['name'], $field_name );
	$block_dir      = dirname( $metadata['file'] );
	$style_file     = realpath( "$block_dir/$style_path" );
	$has_style_file = false !== $style_file;
	$version        = ! $is_core_block && isset( $metadata['version'] ) ? $metadata['version'] : false;
	$style_uri      = $has_style_file ? $style_uri : false;
	$result         = wp_register_style(
		$style_handle,
		$style_uri,
		array(),
		$version
	);
	if ( file_exists( str_replace( '.css', '-rtl.css', $style_file ) ) ) {
		wp_style_add_data( $style_handle, 'rtl', 'replace' );
	}
	if ( $has_style_file ) {
		wp_style_add_data( $style_handle, 'path', $style_file );
	}

	$rtl_file = str_replace( "$suffix.css", "-rtl$suffix.css", $style_file );
	if ( is_rtl() && file_exists( $rtl_file ) ) {
		wp_style_add_data( $style_handle, 'path', $rtl_file );
	}

	return $result ? $style_handle : false;
}

/**
 * Registers a block type from the metadata stored in the `block.json` file.
 *
 * @since 5.5.0
 *
 * @param string $file_or_folder Path to the JSON file with metadata definition for
 *                               the block or path to the folder where the `block.json` file is located.
 * @param array  $args           Optional. Array of block type arguments. Accepts any public property
 *                               of `WP_Block_Type`. See WP_Block_Type::__construct() for information
 *                               on accepted arguments. Default empty array.
 * @return WP_Block_Type|false The registered block type on success, or false on failure.
 */
function register_block_type_from_metadata( $file_or_folder, $args = array() ) {
	$filename      = 'block.json';
	$metadata_file = ( substr( $file_or_folder, -strlen( $filename ) ) !== $filename ) ?
		trailingslashit( $file_or_folder ) . $filename :
		$file_or_folder;
	if ( ! file_exists( $metadata_file ) ) {
		return false;
	}

	$metadata = json_decode( file_get_contents( $metadata_file ), true );
	if ( ! is_array( $metadata ) || empty( $metadata['name'] ) ) {
		return false;
	}
	$metadata['file'] = $metadata_file;

	/**
	 * Filters the metadata provided for registering a block type.
	 *
	 * @since 5.7.0
	 *
	 * @param array $metadata Metadata for registering a block type.
	 */
	$metadata = apply_filters( 'block_type_metadata', $metadata );

	// Add `style` and `editor_style` for core blocks if missing.
	if ( ! empty( $metadata['name'] ) && 0 === strpos( $metadata['name'], 'core/' ) ) {
		$block_name = str_replace( 'core/', '', $metadata['name'] );

		if ( ! isset( $metadata['style'] ) ) {
			$metadata['style'] = "wp-block-$block_name";
		}
		if ( ! isset( $metadata['editorStyle'] ) ) {
			$metadata['editorStyle'] = "wp-block-{$block_name}-editor";
		}
	}

	$settings          = array();
	$property_mappings = array(
		'title'           => 'title',
		'category'        => 'category',
		'parent'          => 'parent',
		'icon'            => 'icon',
		'description'     => 'description',
		'keywords'        => 'keywords',
		'attributes'      => 'attributes',
		'providesContext' => 'provides_context',
		'usesContext'     => 'uses_context',
		'supports'        => 'supports',
		'styles'          => 'styles',
		'example'         => 'example',
		'apiVersion'      => 'api_version',
	);

	foreach ( $property_mappings as $key => $mapped_key ) {
		if ( isset( $metadata[ $key ] ) ) {
			$value = $metadata[ $key ];
			if ( empty( $metadata['textdomain'] ) ) {
				$settings[ $mapped_key ] = $value;
				continue;
			}
			$textdomain = $metadata['textdomain'];
			switch ( $key ) {
				case 'title':
				case 'description':
					// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralContext,WordPress.WP.I18n.NonSingularStringLiteralDomain
					$settings[ $mapped_key ] = translate_with_gettext_context( $value, sprintf( 'block %s', $key ), $textdomain );
					break;
				case 'keywords':
					$settings[ $mapped_key ] = array();
					if ( ! is_array( $value ) ) {
						continue 2;
					}

					foreach ( $value as $keyword ) {
						// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain
						$settings[ $mapped_key ][] = translate_with_gettext_context( $keyword, 'block keyword', $textdomain );
					}

					break;
				case 'styles':
					$settings[ $mapped_key ] = array();
					if ( ! is_array( $value ) ) {
						continue 2;
					}

					foreach ( $value as $style ) {
						if ( ! empty( $style['label'] ) ) {
							// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain
							$style['label'] = translate_with_gettext_context( $style['label'], 'block style label', $textdomain );
						}
						$settings[ $mapped_key ][] = $style;
					}

					break;
				default:
					$settings[ $mapped_key ] = $value;
			}
		}
	}

	if ( ! empty( $metadata['editorScript'] ) ) {
		$settings['editor_script'] = register_block_script_handle(
			$metadata,
			'editorScript'
		);
	}

	if ( ! empty( $metadata['script'] ) ) {
		$settings['script'] = register_block_script_handle(
			$metadata,
			'script'
		);
	}

	if ( ! empty( $metadata['editorStyle'] ) ) {
		$settings['editor_style'] = register_block_style_handle(
			$metadata,
			'editorStyle'
		);
	}

	if ( ! empty( $metadata['style'] ) ) {
		$settings['style'] = register_block_style_handle(
			$metadata,
			'style'
		);
	}

	/**
	 * Filters the settings determined from the block type metadata.
	 *
	 * @since 5.7.0
	 *
	 * @param array $settings Array of determined settings for registering a block type.
	 * @param array $metadata Metadata provided for registering a block type.
	 */
	$settings = apply_filters(
		'block_type_metadata_settings',
		array_merge(
			$settings,
			$args
		),
		$metadata
	);

	return WP_Block_Type_Registry::get_instance()->register(
		$metadata['name'],
		$settings
	);
}

/**
 * Registers a block type. The recommended way is to register a block type using
 * the metadata stored in the `block.json` file.
 *
 * @since 5.0.0
 * @since 5.8.0 First param accepts a path to the `block.json` file.
 *
 * @param string|WP_Block_Type $block_type Block type name including namespace, or alternatively
 *                                         a path to the JSON file with metadata definition for the block,
 *                                         or a path to the folder where the `block.json` file is located,
 *                                         or a complete WP_Block_Type instance.
 *                                         In case a WP_Block_Type is provided, the $args parameter will be ignored.
 * @param array                $args       Optional. Array of block type arguments. Accepts any public property
 *                                         of `WP_Block_Type`. See WP_Block_Type::__construct() for information
 *                                         on accepted arguments. Default empty array.
 *
 * @return WP_Block_Type|false The registered block type on success, or false on failure.
 */
function register_block_type( $block_type, $args = array() ) {
	if ( is_string( $block_type ) && file_exists( $block_type ) ) {
		return register_block_type_from_metadata( $block_type, $args );
	}

	return WP_Block_Type_Registry::get_instance()->register( $block_type, $args );
}

/**
 * Unregisters a block type.
 *
 * @since 5.0.0
 *
 * @param string|WP_Block_Type $name Block type name including namespace, or alternatively
 *                                   a complete WP_Block_Type instance.
 * @return WP_Block_Type|false The unregistered block type on success, or false on failure.
 */
function unregister_block_type( $name ) {
	return WP_Block_Type_Registry::get_instance()->unregister( $name );
}

/**
 * Determine whether a post or content string has blocks.
 *
 * This test optimizes for performance rather than strict accuracy, detecting
 * the pattern of a block but not validating its structure. For strict accuracy,
 * you should use the block parser on post content.
 *
 * @since 5.0.0
 *
 * @see parse_blocks()
 *
 * @param int|string|WP_Post|null $post Optional. Post content, post ID, or post object.
 *                                      Defaults to global $post.
 * @return bool Whether the post has blocks.
 */
function has_blocks( $post = null ) {
	if ( ! is_string( $post ) ) {
		$wp_post = get_post( $post );
		if ( $wp_post instanceof WP_Post ) {
			$post = $wp_post->post_content;
		}
	}

	return false !== strpos( (string) $post, '<!-- wp:' );
}

/**
 * Determine whether a $post or a string contains a specific block type.
 *
 * This test optimizes for performance rather than strict accuracy, detecting
 * whether the block type exists but not validating its structure and not checking
 * reusable blocks. For strict accuracy, you should use the block parser on post content.
 *
 * @since 5.0.0
 *
 * @see parse_blocks()
 *
 * @param string                  $block_name Full block type to look for.
 * @param int|string|WP_Post|null $post       Optional. Post content, post ID, or post object.
 *                                            Defaults to global $post.
 * @return bool Whether the post content contains the specified block.
 */
function has_block( $block_name, $post = null ) {
	if ( ! has_blocks( $post ) ) {
		return false;
	}

	if ( ! is_string( $post ) ) {
		$wp_post = get_post( $post );
		if ( $wp_post instanceof WP_Post ) {
			$post = $wp_post->post_content;
		}
	}

	/*
	 * Normalize block name to include namespace, if provided as non-namespaced.
	 * This matches behavior for WordPress 5.0.0 - 5.3.0 in matching blocks by
	 * their serialized names.
	 */
	if ( false === strpos( $block_name, '/' ) ) {
		$block_name = 'core/' . $block_name;
	}

	// Test for existence of block by its fully qualified name.
	$has_block = false !== strpos( $post, '<!-- wp:' . $block_name . ' ' );

	if ( ! $has_block ) {
		/*
		 * If the given block name would serialize to a different name, test for
		 * existence by the serialized form.
		 */
		$serialized_block_name = strip_core_block_namespace( $block_name );
		if ( $serialized_block_name !== $block_name ) {
			$has_block = false !== strpos( $post, '<!-- wp:' . $serialized_block_name . ' ' );
		}
	}

	return $has_block;
}

/**
 * Returns an array of the names of all registered dynamic block types.
 *
 * @since 5.0.0
 *
 * @return string[] Array of dynamic block names.
 */
function get_dynamic_block_names() {
	$dynamic_block_names = array();

	$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
	foreach ( $block_types as $block_type ) {
		if ( $block_type->is_dynamic() ) {
			$dynamic_block_names[] = $block_type->name;
		}
	}

	return $dynamic_block_names;
}

/**
 * Given an array of attributes, returns a string in the serialized attributes
 * format prepared for post content.
 *
 * The serialized result is a JSON-encoded string, with unicode escape sequence
 * substitution for characters which might otherwise interfere with embedding
 * the result in an HTML comment.
 *
 * @since 5.3.1
 *
 * @param array $block_attributes Attributes object.
 * @return string Serialized attributes.
 */
function serialize_block_attributes( $block_attributes ) {
	$encoded_attributes = json_encode( $block_attributes );
	$encoded_attributes = preg_replace( '/--/', '\\u002d\\u002d', $encoded_attributes );
	$encoded_attributes = preg_replace( '/</', '\\u003c', $encoded_attributes );
	$encoded_attributes = preg_replace( '/>/', '\\u003e', $encoded_attributes );
	$encoded_attributes = preg_replace( '/&/', '\\u0026', $encoded_attributes );
	// Regex: /\\"/
	$encoded_attributes = preg_replace( '/\\\\"/', '\\u0022', $encoded_attributes );

	return $encoded_attributes;
}

/**
 * Returns the block name to use for serialization. This will remove the default
 * "core/" namespace from a block name.
 *
 * @since 5.3.1
 *
 * @param string $block_name Original block name.
 * @return string Block name to use for serialization.
 */
function strip_core_block_namespace( $block_name = null ) {
	if ( is_string( $block_name ) && 0 === strpos( $block_name, 'core/' ) ) {
		return substr( $block_name, 5 );
	}

	return $block_name;
}

/**
 * Returns the content of a block, including comment delimiters.
 *
 * @since 5.3.1
 *
 * @param string|null $block_name       Block name. Null if the block name is unknown,
 *                                      e.g. Classic blocks have their name set to null.
 * @param array       $block_attributes Block attributes.
 * @param string      $block_content    Block save content.
 * @return string Comment-delimited block content.
 */
function get_comment_delimited_block_content( $block_name, $block_attributes, $block_content ) {
	if ( is_null( $block_name ) ) {
		return $block_content;
	}

	$serialized_block_name = strip_core_block_namespace( $block_name );
	$serialized_attributes = empty( $block_attributes ) ? '' : serialize_block_attributes( $block_attributes ) . ' ';

	if ( empty( $block_content ) ) {
		return sprintf( '<!-- wp:%s %s/-->', $serialized_block_name, $serialized_attributes );
	}

	return sprintf(
		'<!-- wp:%s %s-->%s<!-- /wp:%s -->',
		$serialized_block_name,
		$serialized_attributes,
		$block_content,
		$serialized_block_name
	);
}

/**
 * Returns the content of a block, including comment delimiters, serializing all
 * attributes from the given parsed block.
 *
 * This should be used when preparing a block to be saved to post content.
 * Prefer `render_block` when preparing a block for display. Unlike
 * `render_block`, this does not evaluate a block's `render_callback`, and will
 * instead preserve the markup as parsed.
 *
 * @since 5.3.1
 *
 * @param WP_Block_Parser_Block $block A single parsed block object.
 * @return string String of rendered HTML.
 */
function serialize_block( $block ) {
	$block_content = '';

	$index = 0;
	foreach ( $block['innerContent'] as $chunk ) {
		$block_content .= is_string( $chunk ) ? $chunk : serialize_block( $block['innerBlocks'][ $index++ ] );
	}

	if ( ! is_array( $block['attrs'] ) ) {
		$block['attrs'] = array();
	}

	return get_comment_delimited_block_content(
		$block['blockName'],
		$block['attrs'],
		$block_content
	);
}

/**
 * Returns a joined string of the aggregate serialization of the given parsed
 * blocks.
 *
 * @since 5.3.1
 *
 * @param WP_Block_Parser_Block[] $blocks Parsed block objects.
 * @return string String of rendered HTML.
 */
function serialize_blocks( $blocks ) {
	return implode( '', array_map( 'serialize_block', $blocks ) );
}

/**
 * Filters and sanitizes block content to remove non-allowable HTML from
 * parsed block attribute values.
 *
 * @since 5.3.1
 *
 * @param string         $text              Text that may contain block content.
 * @param array[]|string $allowed_html      An array of allowed HTML elements
 *                                          and attributes, or a context name
 *                                          such as 'post'.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 * @return string The filtered and sanitized content result.
 */
function filter_block_content( $text, $allowed_html = 'post', $allowed_protocols = array() ) {
	$result = '';

	$blocks = parse_blocks( $text );
	foreach ( $blocks as $block ) {
		$block   = filter_block_kses( $block, $allowed_html, $allowed_protocols );
		$result .= serialize_block( $block );
	}

	return $result;
}

/**
 * Filters and sanitizes a parsed block to remove non-allowable HTML from block
 * attribute values.
 *
 * @since 5.3.1
 *
 * @param WP_Block_Parser_Block $block             The parsed block object.
 * @param array[]|string        $allowed_html      An array of allowed HTML
 *                                                 elements and attributes, or a
 *                                                 context name such as 'post'.
 * @param string[]              $allowed_protocols Allowed URL protocols.
 * @return array The filtered and sanitized block object result.
 */
function filter_block_kses( $block, $allowed_html, $allowed_protocols = array() ) {
	$block['attrs'] = filter_block_kses_value( $block['attrs'], $allowed_html, $allowed_protocols );

	if ( is_array( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $i => $inner_block ) {
			$block['innerBlocks'][ $i ] = filter_block_kses( $inner_block, $allowed_html, $allowed_protocols );
		}
	}

	return $block;
}

/**
 * Filters and sanitizes a parsed block attribute value to remove non-allowable
 * HTML.
 *
 * @since 5.3.1
 *
 * @param string[]|string $value             The attribute value to filter.
 * @param array[]|string  $allowed_html      An array of allowed HTML elements
 *                                           and attributes, or a context name
 *                                           such as 'post'.
 * @param string[]        $allowed_protocols Array of allowed URL protocols.
 * @return string[]|string The filtered and sanitized result.
 */
function filter_block_kses_value( $value, $allowed_html, $allowed_protocols = array() ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $inner_value ) {
			$filtered_key   = filter_block_kses_value( $key, $allowed_html, $allowed_protocols );
			$filtered_value = filter_block_kses_value( $inner_value, $allowed_html, $allowed_protocols );

			if ( $filtered_key !== $key ) {
				unset( $value[ $key ] );
			}

			$value[ $filtered_key ] = $filtered_value;
		}
	} elseif ( is_string( $value ) ) {
		return wp_kses( $value, $allowed_html, $allowed_protocols );
	}

	return $value;
}

/**
 * Parses blocks out of a content string, and renders those appropriate for the excerpt.
 *
 * As the excerpt should be a small string of text relevant to the full post content,
 * this function renders the blocks that are most likely to contain such text.
 *
 * @since 5.0.0
 *
 * @param string $content The content to parse.
 * @return string The parsed and filtered content.
 */
function excerpt_remove_blocks( $content ) {
	$allowed_inner_blocks = array(
		// Classic blocks have their blockName set to null.
		null,
		'core/freeform',
		'core/heading',
		'core/html',
		'core/list',
		'core/media-text',
		'core/paragraph',
		'core/preformatted',
		'core/pullquote',
		'core/quote',
		'core/table',
		'core/verse',
	);

	$allowed_wrapper_blocks = array(
		'core/columns',
		'core/column',
		'core/group',
	);

	/**
	 * Filters the list of blocks that can be used as wrapper blocks, allowing
	 * excerpts to be generated from the `innerBlocks` of these wrappers.
	 *
	 * @since 5.8.0
	 *
	 * @param array $allowed_wrapper_blocks The list of allowed wrapper blocks.
	 */
	$allowed_wrapper_blocks = apply_filters( 'excerpt_allowed_wrapper_blocks', $allowed_wrapper_blocks );

	$allowed_blocks = array_merge( $allowed_inner_blocks, $allowed_wrapper_blocks );

	/**
	 * Filters the list of blocks that can contribute to the excerpt.
	 *
	 * If a dynamic block is added to this list, it must not generate another
	 * excerpt, as this will cause an infinite loop to occur.
	 *
	 * @since 5.0.0
	 *
	 * @param array $allowed_blocks The list of allowed blocks.
	 */
	$allowed_blocks = apply_filters( 'excerpt_allowed_blocks', $allowed_blocks );
	$blocks         = parse_blocks( $content );
	$output         = '';

	foreach ( $blocks as $block ) {
		if ( in_array( $block['blockName'], $allowed_blocks, true ) ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				if ( in_array( $block['blockName'], $allowed_wrapper_blocks, true ) ) {
					$output .= _excerpt_render_inner_blocks( $block, $allowed_blocks );
					continue;
				}

				// Skip the block if it has disallowed or nested inner blocks.
				foreach ( $block['innerBlocks'] as $inner_block ) {
					if (
						! in_array( $inner_block['blockName'], $allowed_inner_blocks, true ) ||
						! empty( $inner_block['innerBlocks'] )
					) {
						continue 2;
					}
				}
			}

			$output .= render_block( $block );
		}
	}

	return $output;
}

/**
 * Render inner blocks from the allowed wrapper blocks
 * for generating an excerpt.
 *
 * @since 5.8
 * @access private
 *
 * @param array $parsed_block   The parsed block.
 * @param array $allowed_blocks The list of allowed inner blocks.
 * @return string The rendered inner blocks.
 */
function _excerpt_render_inner_blocks( $parsed_block, $allowed_blocks ) {
	$output = '';

	foreach ( $parsed_block['innerBlocks'] as $inner_block ) {
		if ( ! in_array( $inner_block['blockName'], $allowed_blocks, true ) ) {
			continue;
		}

		if ( empty( $inner_block['innerBlocks'] ) ) {
			$output .= render_block( $inner_block );
		} else {
			$output .= _excerpt_render_inner_blocks( $inner_block, $allowed_blocks );
		}
	}

	return $output;
}

/**
 * Renders a single block into a HTML string.
 *
 * @since 5.0.0
 *
 * @global WP_Post  $post     The post to edit.
 *
 * @param array $parsed_block A single parsed block object.
 * @return string String of rendered HTML.
 */
function render_block( $parsed_block ) {
	global $post;

	/**
	 * Allows render_block() to be short-circuited, by returning a non-null value.
	 *
	 * @since 5.1.0
	 *
	 * @param string|null $pre_render   The pre-rendered content. Default null.
	 * @param array       $parsed_block The block being rendered.
	 */
	$pre_render = apply_filters( 'pre_render_block', null, $parsed_block );
	if ( ! is_null( $pre_render ) ) {
		return $pre_render;
	}

	$source_block = $parsed_block;

	/**
	 * Filters the block being rendered in render_block(), before it's processed.
	 *
	 * @since 5.1.0
	 *
	 * @param array $parsed_block The block being rendered.
	 * @param array $source_block An un-modified copy of $parsed_block, as it appeared in the source content.
	 */
	$parsed_block = apply_filters( 'render_block_data', $parsed_block, $source_block );

	$context = array();

	if ( $post instanceof WP_Post ) {
		$context['postId'] = $post->ID;

		/*
		 * The `postType` context is largely unnecessary server-side, since the ID
		 * is usually sufficient on its own. That being said, since a block's
		 * manifest is expected to be shared between the server and the client,
		 * it should be included to consistently fulfill the expectation.
		 */
		$context['postType'] = $post->post_type;
	}

	/**
	 * Filters the default context provided to a rendered block.
	 *
	 * @since 5.5.0
	 *
	 * @param array $context      Default context.
	 * @param array $parsed_block Block being rendered, filtered by `render_block_data`.
	 */
	$context = apply_filters( 'render_block_context', $context, $parsed_block );

	$block = new WP_Block( $parsed_block, $context );

	return $block->render();
}

/**
 * Parses blocks out of a content string.
 *
 * @since 5.0.0
 *
 * @param string $content Post content.
 * @return array[] Array of parsed block objects.
 */
function parse_blocks( $content ) {
	/**
	 * Filter to allow plugins to replace the server-side block parser
	 *
	 * @since 5.0.0
	 *
	 * @param string $parser_class Name of block parser class.
	 */
	$parser_class = apply_filters( 'block_parser_class', 'WP_Block_Parser' );

	$parser = new $parser_class();
	return $parser->parse( $content );
}

/**
 * Parses dynamic blocks out of `post_content` and re-renders them.
 *
 * @since 5.0.0
 *
 * @param string $content Post content.
 * @return string Updated post content.
 */
function do_blocks( $content ) {
	$blocks = parse_blocks( $content );
	$output = '';

	foreach ( $blocks as $block ) {
		$output .= render_block( $block );
	}

	// If there are blocks in this content, we shouldn't run wpautop() on it later.
	$priority = has_filter( 'the_content', 'wpautop' );
	if ( false !== $priority && doing_filter( 'the_content' ) && has_blocks( $content ) ) {
		remove_filter( 'the_content', 'wpautop', $priority );
		add_filter( 'the_content', '_restore_wpautop_hook', $priority + 1 );
	}

	return $output;
}

/**
 * If do_blocks() needs to remove wpautop() from the `the_content` filter, this re-adds it afterwards,
 * for subsequent `the_content` usage.
 *
 * @access private
 *
 * @since 5.0.0
 *
 * @param string $content The post content running through this filter.
 * @return string The unmodified content.
 */
function _restore_wpautop_hook( $content ) {
	$current_priority = has_filter( 'the_content', '_restore_wpautop_hook' );

	add_filter( 'the_content', 'wpautop', $current_priority - 1 );
	remove_filter( 'the_content', '_restore_wpautop_hook', $current_priority );

	return $content;
}

/**
 * Returns the current version of the block format that the content string is using.
 *
 * If the string doesn't contain blocks, it returns 0.
 *
 * @since 5.0.0
 *
 * @param string $content Content to test.
 * @return int The block format version is 1 if the content contains one or more blocks, 0 otherwise.
 */
function block_version( $content ) {
	return has_blocks( $content ) ? 1 : 0;
}

/**
 * Registers a new block style.
 *
 * @since 5.3.0
 *
 * @param string $block_name       Block type name including namespace.
 * @param array  $style_properties Array containing the properties of the style name,
 *                                 label, style (name of the stylesheet to be enqueued),
 *                                 inline_style (string containing the CSS to be added).
 * @return bool True if the block style was registered with success and false otherwise.
 */
function register_block_style( $block_name, $style_properties ) {
	return WP_Block_Styles_Registry::get_instance()->register( $block_name, $style_properties );
}

/**
 * Unregisters a block style.
 *
 * @since 5.3.0
 *
 * @param string $block_name       Block type name including namespace.
 * @param string $block_style_name Block style name.
 * @return bool True if the block style was unregistered with success and false otherwise.
 */
function unregister_block_style( $block_name, $block_style_name ) {
	return WP_Block_Styles_Registry::get_instance()->unregister( $block_name, $block_style_name );
}

/**
 * Checks whether the current block type supports the feature requested.
 *
 * @since 5.8.0
 *
 * @param WP_Block_Type $block_type Block type to check for support.
 * @param string        $feature    Name of the feature to check support for.
 * @param mixed         $default    Optional. Fallback value for feature support. Default false.
 * @return bool Whether the feature is supported.
 */
function block_has_support( $block_type, $feature, $default = false ) {
	$block_support = $default;
	if ( $block_type && property_exists( $block_type, 'supports' ) ) {
		$block_support = _wp_array_get( $block_type->supports, $feature, $default );
	}

	return true === $block_support || is_array( $block_support );
}

/**
 * Converts typography keys declared under `supports.*` to `supports.typography.*`.
 *
 * Displays a `_doing_it_wrong()` notice when a block using the older format is detected.
 *
 * @since 5.8.0
 *
 * @param array $metadata Metadata for registering a block type.
 * @return array Filtered metadata for registering a block type.
 */
function wp_migrate_old_typography_shape( $metadata ) {
	if ( ! isset( $metadata['supports'] ) ) {
		return $metadata;
	}

	$typography_keys = array(
		'__experimentalFontFamily',
		'__experimentalFontStyle',
		'__experimentalFontWeight',
		'__experimentalLetterSpacing',
		'__experimentalTextDecoration',
		'__experimentalTextTransform',
		'fontSize',
		'lineHeight',
	);

	foreach ( $typography_keys as $typography_key ) {
		$support_for_key = _wp_array_get( $metadata['supports'], array( $typography_key ), null );

		if ( null !== $support_for_key ) {
			_doing_it_wrong(
				'register_block_type_from_metadata()',
				sprintf(
					/* translators: 1: Block type, 2: Typography supports key, e.g: fontSize, lineHeight, etc. 3: block.json, 4: Old metadata key, 5: New metadata key. */
					__( 'Block "%1$s" is declaring %2$s support in %3$s file under %4$s. %2$s support is now declared under %5$s.' ),
					$metadata['name'],
					"<code>$typography_key</code>",
					'<code>block.json</code>',
					"<code>supports.$typography_key</code>",
					"<code>supports.typography.$typography_key</code>"
				),
				'5.8.0'
			);

			_wp_array_set( $metadata['supports'], array( 'typography', $typography_key ), $support_for_key );
			unset( $metadata['supports'][ $typography_key ] );
		}
	}

	return $metadata;
}

/**
 * Helper function that constructs a WP_Query args array from
 * a `Query` block properties.
 *
 * It's used in Query Loop, Query Pagination Numbers and Query Pagination Next blocks.
 *
 * @since 5.8.0
 *
 * @param WP_Block $block Block instance.
 * @param int      $page  Current query's page.
 *
 * @return array Returns the constructed WP_Query arguments.
 */
function build_query_vars_from_query_block( $block, $page ) {
	$query = array(
		'post_type'    => 'post',
		'order'        => 'DESC',
		'orderby'      => 'date',
		'post__not_in' => array(),
	);

	if ( isset( $block->context['query'] ) ) {
		if ( ! empty( $block->context['query']['postType'] ) ) {
			$post_type_param = $block->context['query']['postType'];
			if ( is_post_type_viewable( $post_type_param ) ) {
				$query['post_type'] = $post_type_param;
			}
		}
		if ( isset( $block->context['query']['sticky'] ) && ! empty( $block->context['query']['sticky'] ) ) {
			$sticky = get_option( 'sticky_posts' );
			if ( 'only' === $block->context['query']['sticky'] ) {
				$query['post__in'] = $sticky;
			} else {
				$query['post__not_in'] = array_merge( $query['post__not_in'], $sticky );
			}
		}
		if ( ! empty( $block->context['query']['exclude'] ) ) {
			$excluded_post_ids     = array_map( 'intval', $block->context['query']['exclude'] );
			$excluded_post_ids     = array_filter( $excluded_post_ids );
			$query['post__not_in'] = array_merge( $query['post__not_in'], $excluded_post_ids );
		}
		if (
			isset( $block->context['query']['perPage'] ) &&
			is_numeric( $block->context['query']['perPage'] )
		) {
			$per_page = absint( $block->context['query']['perPage'] );
			$offset   = 0;

			if (
				isset( $block->context['query']['offset'] ) &&
				is_numeric( $block->context['query']['offset'] )
			) {
				$offset = absint( $block->context['query']['offset'] );
			}

			$query['offset']         = ( $per_page * ( $page - 1 ) ) + $offset;
			$query['posts_per_page'] = $per_page;
		}
		if ( ! empty( $block->context['query']['categoryIds'] ) ) {
			$term_ids              = array_map( 'intval', $block->context['query']['categoryIds'] );
			$term_ids              = array_filter( $term_ids );
			$query['category__in'] = $term_ids;
		}
		if ( ! empty( $block->context['query']['tagIds'] ) ) {
			$term_ids         = array_map( 'intval', $block->context['query']['tagIds'] );
			$term_ids         = array_filter( $term_ids );
			$query['tag__in'] = $term_ids;
		}
		if (
			isset( $block->context['query']['order'] ) &&
				in_array( strtoupper( $block->context['query']['order'] ), array( 'ASC', 'DESC' ), true )
		) {
			$query['order'] = strtoupper( $block->context['query']['order'] );
		}
		if ( isset( $block->context['query']['orderBy'] ) ) {
			$query['orderby'] = $block->context['query']['orderBy'];
		}
		if (
			isset( $block->context['query']['author'] ) &&
			(int) $block->context['query']['author'] > 0
		) {
			$query['author'] = (int) $block->context['query']['author'];
		}
		if ( ! empty( $block->context['query']['search'] ) ) {
			$query['s'] = $block->context['query']['search'];
		}
	}
	return $query;
}
