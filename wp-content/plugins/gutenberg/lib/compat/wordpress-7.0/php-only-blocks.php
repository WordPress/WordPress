<?php
/**
 * PHP-only block registration for WordPress 6.9+
 *
 * @package gutenberg
 */

/**
 * Expose blocks with auto_register flag for ServerSideRender in the editor.
 *
 * Detects blocks that have the auto_register flag set in their supports
 * and passes them to JavaScript for auto-registration with ServerSideRender.
 */
function gutenberg_register_auto_register_blocks() {
	$auto_register_blocks = array();
	$registered_blocks    = WP_Block_Type_Registry::get_instance()->get_all_registered();

	foreach ( $registered_blocks as $block_name => $block_type ) {
		$has_auto_register_flag = ! empty( $block_type->auto_register ) || ! empty( $block_type->supports['auto_register'] );
		$has_render_callback    = ! empty( $block_type->render_callback );

		if ( $has_auto_register_flag && $has_render_callback ) {
			$auto_register_blocks[] = $block_name;
		}
	}

	if ( ! empty( $auto_register_blocks ) ) {
		wp_add_inline_script(
			'wp-block-library',
			sprintf( 'window.__unstableAutoRegisterBlocks = %s;', wp_json_encode( $auto_register_blocks ) ),
			'before'
		);
	}
}

add_action( 'enqueue_block_editor_assets', 'gutenberg_register_auto_register_blocks', 5 );
