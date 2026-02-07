<?php

/**
 * Client Assets compatibility functions for WordPress 6.9.
 *
 * Hooks into `render_block_data` to detect blocks that support preloading
 * during client side navigation (used by interactivity router). This is addressed in core in:
 * https://github.com/WordPress/wordpress-develop/pull/10357
 *
 * @package gutenberg
 */

if ( ! method_exists( 'WP_Interactivity_API', 'add_client_navigation_support_to_script_module' ) ) {
	/**
	 * Access the shared static variable for interactive script modules.
	 *
	 * @param string|null $script_module_id The script module ID to register, or null to get the list.
	 * @return array<string, true> Associative array of script module ID => true.
	 */
	function gutenberg_interactive_script_modules_registry( $script_module_id = null ) {
		static $interactive_script_modules = array(
			'@wordpress/block-library/navigation/view-js-module' => true,
			'@wordpress/block-library/query/view-js-module'      => true,
			'@wordpress/block-library/image/view-js-module'      => true,
		);
		if ( null !== $script_module_id ) {
			$interactive_script_modules[ $script_module_id . '-js-module' ] = true;
		}
		return $interactive_script_modules;
	}

	/**
	 * Adds `data-wp-router-options` attribute to script modules registered as interactive.
	 *
	 * @param array<string, string|true>|mixed $attributes Script attributes.
	 * @return array<string, string|true> Filtered script attributes.
	 */
	function gutenberg_script_module_add_router_options_attributes( $attributes ): array {
		if ( ! is_array( $attributes ) ) {
			return $attributes;
		}
		if ( isset( $attributes['id'] ) && array_key_exists( $attributes['id'], gutenberg_interactive_script_modules_registry() ) ) {
			$attributes['data-wp-router-options'] = wp_json_encode( array( 'loadOnClientNavigation' => true ) );
		}
		return $attributes;
	}
	add_filter( 'wp_script_attributes', 'gutenberg_script_module_add_router_options_attributes' );

	function gutenberg_script_module_add_load_on_client_navigation_to_script_modules( $parsed_block ) {
		if ( ! isset( $parsed_block['blockName'] ) ) {
			return $parsed_block;
		}

		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $parsed_block['blockName'] );

		$supports_interactivity        = isset( $block_type->supports['interactivity'] );
		$supports_interactivity_array  = $supports_interactivity && is_array( $block_type->supports['interactivity'] );
		$is_fully_interactive          = $supports_interactivity && true === $block_type->supports['interactivity'];
		$is_supports_interactive       = $supports_interactivity_array && isset( $block_type->supports['interactivity']['interactive'] ) && true === $block_type->supports['interactivity']['interactive'];
		$is_supports_client_navigation = $supports_interactivity_array && isset( $block_type->supports['interactivity']['clientNavigation'] ) && true === $block_type->supports['interactivity']['clientNavigation'];

		if ( $is_fully_interactive || ( $is_supports_interactive && $is_supports_client_navigation ) ) {
			foreach ( $block_type->view_script_module_ids as $script_module_id ) {
				gutenberg_interactive_script_modules_registry( $script_module_id );
			}
		}

		return $parsed_block;
	}
	add_action( 'render_block_data', 'gutenberg_script_module_add_load_on_client_navigation_to_script_modules', 10, 1 );
}
