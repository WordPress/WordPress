<?php
/**
 * Temporary compatibility shims for block APIs present in Gutenberg.
 *
 * @package gutenberg
 */

if ( ! function_exists( 'wp_enqueue_block_view_script' ) ) {
	/**
	 * Enqueues a frontend script for a specific block.
	 *
	 * Scripts enqueued using this function will only get printed
	 * when the block gets rendered on the frontend.
	 *
	 * @since 6.2.0
	 *
	 * @param string $block_name The block name, including namespace.
	 * @param array  $args       An array of arguments [handle,src,deps,ver,media,textdomain].
	 *
	 * @return void
	 */
	function wp_enqueue_block_view_script( $block_name, $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'handle'     => '',
				'src'        => '',
				'deps'       => array(),
				'ver'        => false,
				'in_footer'  => false,

				// Additional args to allow translations for the script's textdomain.
				'textdomain' => '',
			)
		);

		/**
		 * Callback function to register and enqueue scripts.
		 *
		 * @param string $content When the callback is used for the render_block filter,
		 *                        the content needs to be returned so the function parameter
		 *                        is to ensure the content exists.
		 * @return string Block content.
		 */
		$callback = static function ( $content, $block ) use ( $args, $block_name ) {

			// Sanity check.
			if ( empty( $block['blockName'] ) || $block_name !== $block['blockName'] ) {
				return $content;
			}

			// Register the stylesheet.
			if ( ! empty( $args['src'] ) ) {
				wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
			}

			// Enqueue the stylesheet.
			wp_enqueue_script( $args['handle'] );

			// If a textdomain is defined, use it to set the script translations.
			if ( ! empty( $args['textdomain'] ) && in_array( 'wp-i18n', $args['deps'], true ) ) {
				wp_set_script_translations( $args['handle'], $args['textdomain'], $args['domainpath'] );
			}

			return $content;
		};

		/*
		 * The filter's callback here is an anonymous function because
		 * using a named function in this case is not possible.
		 *
		 * The function cannot be unhooked, however, users are still able
		 * to dequeue the script registered/enqueued by the callback
		 * which is why in this case, using an anonymous function
		 * was deemed acceptable.
		 */
		add_filter( 'render_block', $callback, 10, 2 );
	}
}

/**
 * Registers a new block style for one or more block types.
 *
 * WP_Block_Styles_Registry was marked as `final` in core so it cannot be
 * updated via Gutenberg to allow registration of a style across multiple
 * block types as well as with an optional style object. This function will
 * support the desired functionality until the styles registry can be updated
 * in core.
 *
 * @param string|array $block_name       Block type name including namespace or array of namespaced block type names.
 * @param array        $style_properties Array containing the properties of the style name, label,
 *                                       style_handle (name of the stylesheet to be enqueued),
 *                                       inline_style (string containing the CSS to be added),
 *                                       style_data (theme.json-like object to generate CSS from).
 *
 * @return bool True if all block styles were registered with success and false otherwise.
 */
function gutenberg_register_block_style( $block_name, $style_properties ) {
	if ( ! is_string( $block_name ) && ! is_array( $block_name ) ) {
		_doing_it_wrong(
			__METHOD__,
			__( 'Block name must be a string or array.', 'gutenberg' ),
			'6.6.0'
		);

		return false;
	}

	$block_names = is_string( $block_name ) ? array( $block_name ) : $block_name;
	$result      = true;

	foreach ( $block_names as $name ) {
		if ( ! WP_Block_Styles_Registry::get_instance()->register( $name, $style_properties ) ) {
			$result = false;
		}
	}

	return $result;
}

/**
 * Additional data to expose to the view script module in the Form block.
 */
function gutenberg_block_core_form_view_script_module( $data ) {
	if ( ! gutenberg_is_experiment_enabled( 'gutenberg-form-blocks' ) ) {
		return $data;
	}

	$data['nonce']   = wp_create_nonce( 'wp-block-form' );
	$data['ajaxUrl'] = admin_url( 'admin-ajax.php' );
	$data['action']  = 'wp_block_form_email_submit';

	return $data;
}
add_filter(
	'script_module_data_@wordpress/block-library/form/view',
	'gutenberg_block_core_form_view_script_module'
);
