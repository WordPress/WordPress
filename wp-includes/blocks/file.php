<?php
/**
 * Server-side rendering of the `core/file` block.
 *
 * @package WordPress
 */

if ( defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN ) {
	/**
	 * Replaces view script for the File block with version using Interactivity API.
	 *
	 * @param array $metadata Block metadata as read in via block.json.
	 *
	 * @return array Filtered block type metadata.
	 */
	function gutenberg_block_core_file_update_interactive_view_script( $metadata ) {
		if ( 'core/file' === $metadata['name'] ) {
			$metadata['viewScript'] = array( 'file:./interactivity.min.js' );
		}
		return $metadata;
	}
	add_filter( 'block_type_metadata', 'gutenberg_block_core_file_update_interactive_view_script', 10, 1 );
}

/**
 * When the `core/file` block is rendering, check if we need to enqueue the `'wp-block-file-view` script.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the block content.
 */
function render_block_core_file( $attributes, $content, $block ) {
	$should_load_view_script = ! empty( $attributes['displayPreview'] );
	$view_js_file            = 'wp-block-file-view';
	// If the script already exists, there is no point in removing it from viewScript.
	if ( ! wp_script_is( $view_js_file ) ) {
		$script_handles = $block->block_type->view_script_handles;

		// If the script is not needed, and it is still in the `view_script_handles`, remove it.
		if ( ! $should_load_view_script && in_array( $view_js_file, $script_handles, true ) ) {
			$block->block_type->view_script_handles = array_diff( $script_handles, array( $view_js_file ) );
		}
		// If the script is needed, but it was previously removed, add it again.
		if ( $should_load_view_script && ! in_array( $view_js_file, $script_handles, true ) ) {
			$block->block_type->view_script_handles = array_merge( $script_handles, array( $view_js_file ) );
		}
	}

	// Update object's aria-label attribute if present in block HTML.

	// Match an aria-label attribute from an object tag.
	$pattern = '@<object.+(?<attribute>aria-label="(?<filename>[^"]+)?")@i';
	$content = preg_replace_callback(
		$pattern,
		static function ( $matches ) {
			$filename     = ! empty( $matches['filename'] ) ? $matches['filename'] : '';
			$has_filename = ! empty( $filename ) && 'PDF embed' !== $filename;
			$label        = $has_filename ?
				sprintf(
					/* translators: %s: filename. */
					__( 'Embed of %s.' ),
					$filename
				)
				: __( 'PDF embed' );

			return str_replace( $matches['attribute'], sprintf( 'aria-label="%s"', $label ), $matches[0] );
		},
		$content
	);

	// If it uses the Interactivity API, add the directives.
	if ( defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN && $should_load_view_script ) {
		$processor = new WP_HTML_Tag_Processor( $content );
		$processor->next_tag();
		$processor->set_attribute( 'data-wp-interactive', '' );
		$processor->next_tag( 'object' );
		$processor->set_attribute( 'data-wp-bind--hidden', '!selectors.core.file.hasPdfPreview' );
		$processor->set_attribute( 'hidden', true );
		return $processor->get_updated_html();
	}

	return $content;
}

/**
 * Registers the `core/file` block on server.
 */
function register_block_core_file() {
	register_block_type_from_metadata(
		__DIR__ . '/file',
		array(
			'render_callback' => 'render_block_core_file',
		)
	);
}
add_action( 'init', 'register_block_core_file' );
