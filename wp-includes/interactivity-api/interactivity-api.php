<?php
/**
 * Interactivity API: Functions and hooks
 *
 * @package WordPress
 * @subpackage Interactivity API
 * @since 6.5.0
 */

/**
 * Processes the directives on the rendered HTML of the interactive blocks.
 *
 * This processes only one root interactive block at a time because the
 * rendered HTML of that block contains the rendered HTML of all its inner
 * blocks, including any interactive block. It does so by ignoring all the
 * interactive inner blocks until the root interactive block is processed.
 *
 * @since 6.5.0
 *
 * @param array $parsed_block The parsed block.
 * @return array The same parsed block.
 */
function wp_interactivity_process_directives_of_interactive_blocks( array $parsed_block ): array {
	static $root_interactive_block = null;

	/*
	 * Checks whether a root interactive block is already annotated for
	 * processing, and if it is, it ignores the subsequent ones.
	 */
	if ( null === $root_interactive_block ) {
		$block_name = $parsed_block['blockName'];
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block_name );

		if (
			isset( $block_name ) &&
			( ( isset( $block_type->supports['interactivity'] ) && true === $block_type->supports['interactivity'] ) ||
			( isset( $block_type->supports['interactivity']['interactive'] ) && true === $block_type->supports['interactivity']['interactive'] ) )
		) {
			// Annotates the root interactive block for processing.
			$root_interactive_block = array( $block_name, $parsed_block );

			/*
			 * Adds a filter to process the root interactive block once it has
			 * finished rendering.
			 */
			$process_interactive_blocks = static function ( string $content, array $parsed_block ) use ( &$root_interactive_block, &$process_interactive_blocks ): string {
				// Checks whether the current block is the root interactive block.
				list($root_block_name, $root_parsed_block) = $root_interactive_block;
				if ( $root_block_name === $parsed_block['blockName'] && $parsed_block === $root_parsed_block ) {
					// The root interactive blocks has finished rendering, process it.
					$content = wp_interactivity_process_directives( $content );
					// Removes the filter and reset the root interactive block.
					remove_filter( 'render_block_' . $parsed_block['blockName'], $process_interactive_blocks );
					$root_interactive_block = null;
				}
				return $content;
			};

			/*
			 * Uses a priority of 100 to ensure that other filters can add additional
			 * directives before the processing starts.
			 */
			add_filter( 'render_block_' . $block_name, $process_interactive_blocks, 100, 2 );
		}
	}

	return $parsed_block;
}
/*
 * Uses a priority of 100 to ensure that other filters can add additional attributes to
 * $parsed_block before the processing starts.
 */
add_filter( 'render_block_data', 'wp_interactivity_process_directives_of_interactive_blocks', 100, 1 );

/**
 * Retrieves the main WP_Interactivity_API instance.
 *
 * It provides access to the WP_Interactivity_API instance, creating one if it
 * doesn't exist yet.
 *
 * @since 6.5.0
 *
 * @global WP_Interactivity_API $wp_interactivity
 *
 * @return WP_Interactivity_API The main WP_Interactivity_API instance.
 */
function wp_interactivity(): WP_Interactivity_API {
	global $wp_interactivity;
	if ( ! ( $wp_interactivity instanceof WP_Interactivity_API ) ) {
		$wp_interactivity = new WP_Interactivity_API();
	}
	return $wp_interactivity;
}

/**
 * Processes the interactivity directives contained within the HTML content
 * and updates the markup accordingly.
 *
 * @since 6.5.0
 *
 * @param string $html The HTML content to process.
 * @return string The processed HTML content. It returns the original content when the HTML contains unbalanced tags.
 */
function wp_interactivity_process_directives( string $html ): string {
	return wp_interactivity()->process_directives( $html );
}

/**
 * Gets and/or sets the initial state of an Interactivity API store for a
 * given namespace.
 *
 * If state for that store namespace already exists, it merges the new
 * provided state with the existing one.
 *
 * @since 6.5.0
 *
 * @param string $store_namespace The unique store namespace identifier.
 * @param array  $state           Optional. The array that will be merged with the existing state for the specified
 *                                store namespace.
 * @return array The state for the specified store namespace. This will be the updated state if a $state argument was
 *               provided.
 */
function wp_interactivity_state( string $store_namespace, array $state = array() ): array {
	return wp_interactivity()->state( $store_namespace, $state );
}

/**
 * Gets and/or sets the configuration of the Interactivity API for a given
 * store namespace.
 *
 * If configuration for that store namespace exists, it merges the new
 * provided configuration with the existing one.
 *
 * @since 6.5.0
 *
 * @param string $store_namespace The unique store namespace identifier.
 * @param array  $config          Optional. The array that will be merged with the existing configuration for the
 *                                specified store namespace.
 * @return array The configuration for the specified store namespace. This will be the updated configuration if a
 *               $config argument was provided.
 */
function wp_interactivity_config( string $store_namespace, array $config = array() ): array {
	return wp_interactivity()->config( $store_namespace, $config );
}

/**
 * Generates a `data-wp-context` directive attribute by encoding a context
 * array.
 *
 * This helper function simplifies the creation of `data-wp-context` directives
 * by providing a way to pass an array of data, which encodes into a JSON string
 * safe for direct use as a HTML attribute value.
 *
 * Example:
 *
 *     <div <?php echo wp_interactivity_data_wp_context( array( 'isOpen' => true, 'count' => 0 ) ); ?>>
 *
 * @since 6.5.0
 *
 * @param array  $context         The array of context data to encode.
 * @param string $store_namespace Optional. The unique store namespace identifier.
 * @return string A complete `data-wp-context` directive with a JSON encoded value representing the context array and
 *                the store namespace if specified.
 */
function wp_interactivity_data_wp_context( array $context, string $store_namespace = '' ): string {
	return 'data-wp-context=\'' .
		( $store_namespace ? $store_namespace . '::' : '' ) .
		( empty( $context ) ? '{}' : wp_json_encode( $context, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) ) .
		'\'';
}
