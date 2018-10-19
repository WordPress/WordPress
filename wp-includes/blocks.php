<?php
/**
 * Functions related to registering and parsing blocks.
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 5.0.0
 */

/**
 * Registers a block type.
 *
 * @since 5.0.0
 *
 * @param string|WP_Block_Type $name Block type name including namespace, or alternatively a
 *                                   complete WP_Block_Type instance. In case a WP_Block_Type
 *                                   is provided, the $args parameter will be ignored.
 * @param array                $args {
 *     Optional. Array of block type arguments. Any arguments may be defined, however the
 *     ones described below are supported by default. Default empty array.
 *
 *     @type callable $render_callback Callback used to render blocks of this block type.
 * }
 * @return WP_Block_Type|false The registered block type on success, or false on failure.
 */
function register_block_type( $name, $args = array() ) {
	return WP_Block_Type_Registry::get_instance()->register( $name, $args );
}

/**
 * Unregisters a block type.
 *
 * @since 5.0.0
 *
 * @param string|WP_Block_Type $name Block type name including namespace, or alternatively a
 *                                   complete WP_Block_Type instance.
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
 * @see parse_blocks()
 *
 * @param int|string|WP_Post|null $post Optional. Post content, post ID, or post object. Defaults to global $post.
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
 * the block type exists but not validating its structure. For strict accuracy,
 * you should use the block parser on post content.
 *
 * @since 5.0.0
 * @see parse_blocks()
 *
 * @param string                  $block_type Full Block type to look for.
 * @param int|string|WP_Post|null $post Optional. Post content, post ID, or post object. Defaults to global $post.
 * @return bool Whether the post content contains the specified block.
 */
function has_block( $block_type, $post = null ) {
	if ( ! has_blocks( $post ) ) {
		return false;
	}

	if ( ! is_string( $post ) ) {
		$wp_post = get_post( $post );
		if ( $wp_post instanceof WP_Post ) {
			$post = $wp_post->post_content;
		}
	}

	return false !== strpos( $post, '<!-- wp:' . $block_type . ' ' );
}

/**
 * Returns an array of the names of all registered dynamic block types.
 *
 * @since 5.0.0
 *
 * @return array Array of dynamic block names.
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
 * Remove all dynamic blocks from the given content.
 *
 * @since 5.0.0
 *
 * @param string $content Content of the current post.
 * @return string
 */
function strip_dynamic_blocks( $content ) {
	return _recurse_strip_dynamic_blocks( parse_blocks( $content ) );
}

/**
 * Helper function for strip_dynamic_blocks(), to recurse through the block tree.
 *
 * @since 5.0.0
 * @access private
 *
 * @param array $blocks Array of blocks from parse_blocks().
 * @return string HTML from the non-dynamic blocks.
 */
function _recurse_strip_dynamic_blocks( $blocks ) {
	$clean_content  = '';
	$dynamic_blocks = get_dynamic_block_names();

	foreach ( $blocks as $block ) {
		if ( ! in_array( $block['blockName'], $dynamic_blocks ) ) {
			if ( $block['innerBlocks'] ) {
				$clean_content .= _recurse_strip_dynamic_blocks( $block['innerBlocks'] );
			} else {
				$clean_content .= $block['innerHTML'];
			}
		}
	}

	return $clean_content;
}

/**
 * Parses blocks out of a content string.
 *
 * @since 5.0.0
 *
 * @param  string $content Post content.
 * @return array  Array of parsed block objects.
 */
function parse_blocks( $content ) {
	/*
	 * If there are no blocks in the content, return a single block, rather
	 * than wasting time trying to parse the string.
	 */
	if ( ! has_blocks( $content ) ) {
		return array(
			array(
				'blockName'   => null,
				'attrs'       => array(),
				'innerBlocks' => array(),
				'innerHTML'   => $content,
			),
		);
	}

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
 * @global WP_Post $post The post to edit.
 *
 * @param  string $content Post content.
 * @return string Updated post content.
 */
function do_blocks( $content ) {
	$blocks = parse_blocks( $content );
	return _recurse_do_blocks( $blocks, $blocks );
}

/**
 * Helper function for do_blocks(), to recurse through the block tree.
 *
 * @since 5.0.0
 * @access private
 *
 * @param array $blocks     Array of blocks from parse_blocks().
 * @param array $all_blocks The top level array of blocks.
 * @return string The block HTML.
 */
function _recurse_do_blocks( $blocks, $all_blocks ) {
	global $post;

	/*
	 * Back up global post, to restore after render callback.
	 * Allows callbacks to run new WP_Query instances without breaking the global post.
	 */
	$global_post = $post;

	$rendered_content = '';
	$dynamic_blocks   = get_dynamic_block_names();

	foreach ( $blocks as $block ) {
		$block = (array) $block;
		if ( in_array( $block['blockName'], $dynamic_blocks ) ) {
			// Find registered block type. We can assume it exists since we use the
			// `get_dynamic_block_names` function as a source for pattern matching.
			$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

			// Replace dynamic block with server-rendered output.
			$block_content = $block_type->render( (array) $block['attrs'], $block['innerHTML'] );
		} else if ( $block['innerBlocks'] ) {
			$block_content = _recurse_do_blocks( $block['innerBlocks'], $blocks );
		} else {
			$block_content = $block['innerHTML'];
		}

		/**
		 * Filters the content of a single block.
		 *
		 * During the_content, each block is parsed and added to the output individually. This filter allows
		 * that content to be altered immediately before it's appended.
		 *
		 * @since 5.0.0
		 *
		 * @param string $block_content The block content about to be appended.
		 * @param array  $block         The full block, including name and attributes.
		 * @param array  $all_blocks    The array of all blocks being processed.
		 */
		$rendered_content .= apply_filters( 'do_block', $block_content, $block, $all_blocks );

		// Restore global $post.
		$post = $global_post;
	}

	// Strip remaining block comment demarcations.
	$rendered_content = preg_replace( '/<!--\s+\/?wp:.*?-->/m', '', $rendered_content );

	return $rendered_content;
}

/**
 * Returns the current version of the block format that the content string is using.
 *
 * If the string doesn't contain blocks, it returns 0.
 *
 * @since 5.0.0
 *
 * @param string $content Content to test.
 * @return int The block format version.
 */
function block_version( $content ) {
	return has_blocks( $content ) ? 1 : 0;
}
