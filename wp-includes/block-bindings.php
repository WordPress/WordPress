<?php
/**
 * Block Bindings API
 *
 * Contains functions for managing block bindings in WordPress.
 *
 * @package WordPress
 * @subpackage Block Bindings
 * @since 6.5.0
 */

/**
 * Registers a new block bindings source.
 *
 * Registering a source consists of defining a **name** for that source and a callback function specifying
 * how to get a value from that source and pass it to a block attribute.
 *
 * Once a source is registered, any block that supports the Block Bindings API can use a value
 * from that source by setting its `metadata.bindings` attribute to a value that refers to the source.
 *
 * Note that `register_block_bindings_source()` should be called from a handler attached to the `init` hook.
 *
 *
 * ## Example
 *
 * ### Registering a source
 *
 * First, you need to define a function that will be used to get the value from the source.
 *
 *     function my_plugin_get_custom_source_value( array $source_args, $block_instance, string $attribute_name ) {
 *       // Your custom logic to get the value from the source.
 *       // For example, you can use the `$source_args` to look up a value in a custom table or get it from an external API.
 *       $value = $source_args['key'];
 *
 *       return "The value passed to the block is: $value"
 *     }
 *
 * The `$source_args` will contain the arguments passed to the source in the block's
 * `metadata.bindings` attribute. See the example in the "Usage in a block" section below.
 *
 *     function my_plugin_register_block_bindings_sources() {
 *       register_block_bindings_source( 'my-plugin/my-custom-source', array(
 *         'label'              => __( 'My Custom Source', 'my-plugin' ),
 *         'get_value_callback' => 'my_plugin_get_custom_source_value',
 *       ) );
 *     }
 *     add_action( 'init', 'my_plugin_register_block_bindings_sources' );
 *
 * ### Usage in a block
 *
 * In a block's `metadata.bindings` attribute, you can specify the source and
 * its arguments. Such a block will use the source to override the block
 * attribute's value. For example:
 *
 *     <!-- wp:paragraph {
 *       "metadata": {
 *         "bindings": {
 *           "content": {
 *             "source": "my-plugin/my-custom-source",
 *             "args": {
 *               "key": "you can pass any custom arguments here"
 *             }
 *           }
 *         }
 *       }
 *     } -->
 *     <p>Fallback text that gets replaced.</p>
 *     <!-- /wp:paragraph -->
 *
 * @since 6.5.0
 *
 * @param string $source_name       The name of the source. It must be a string containing a namespace prefix, i.e.
 *                                  `my-plugin/my-custom-source`. It must only contain lowercase alphanumeric
 *                                  characters, the forward slash `/` and dashes.
 * @param array  $source_properties {
 *     The array of arguments that are used to register a source.
 *
 *     @type string   $label              The label of the source.
 *     @type callable $get_value_callback A callback executed when the source is processed during block rendering.
 *                                        The callback should have the following signature:
 *
 *                                        `function( $source_args, $block_instance, $attribute_name ): mixed`
 *                                            - @param array    $source_args    Array containing source arguments
 *                                                                              used to look up the override value,
 *                                                                              i.e. {"key": "foo"}.
 *                                            - @param WP_Block $block_instance The block instance.
 *                                            - @param string   $attribute_name The name of an attribute.
 *                                        The callback has a mixed return type; it may return a string to override
 *                                        the block's original value, null, false to remove an attribute, etc.
 *     @type string[] $uses_context       Optional. Array of values to add to block `uses_context` needed by the source.
 * }
 * @return WP_Block_Bindings_Source|false Source when the registration was successful, or `false` on failure.
 */
function register_block_bindings_source( string $source_name, array $source_properties ) {
	return WP_Block_Bindings_Registry::get_instance()->register( $source_name, $source_properties );
}

/**
 * Unregisters a block bindings source.
 *
 * @since 6.5.0
 *
 * @param string $source_name Block bindings source name including namespace.
 * @return WP_Block_Bindings_Source|false The unregistered block bindings source on success and `false` otherwise.
 */
function unregister_block_bindings_source( string $source_name ) {
	return WP_Block_Bindings_Registry::get_instance()->unregister( $source_name );
}

/**
 * Retrieves the list of all registered block bindings sources.
 *
 * @since 6.5.0
 *
 * @return WP_Block_Bindings_Source[] The array of registered block bindings sources.
 */
function get_all_registered_block_bindings_sources() {
	return WP_Block_Bindings_Registry::get_instance()->get_all_registered();
}

/**
 * Retrieves a registered block bindings source.
 *
 * @since 6.5.0
 *
 * @param string $source_name The name of the source.
 * @return WP_Block_Bindings_Source|null The registered block bindings source, or `null` if it is not registered.
 */
function get_block_bindings_source( string $source_name ) {
	return WP_Block_Bindings_Registry::get_instance()->get_registered( $source_name );
}

/**
 * Retrieves the list of block attributes supported by block bindings.
 *
 * @since 6.9.0
 *
 * @param string $block_type The block type whose supported attributes are being retrieved.
 * @return array The list of block attributes that are supported by block bindings.
 */
function get_block_bindings_supported_attributes( $block_type ) {
	$block_bindings_supported_attributes = array(
		'core/paragraph'          => array( 'content' ),
		'core/heading'            => array( 'content' ),
		'core/image'              => array( 'id', 'url', 'title', 'alt', 'caption' ),
		'core/button'             => array( 'url', 'text', 'linkTarget', 'rel' ),
		'core/post-date'          => array( 'datetime' ),
		'core/navigation-link'    => array( 'url' ),
		'core/navigation-submenu' => array( 'url' ),
	);

	$supported_block_attributes =
		isset( $block_type, $block_bindings_supported_attributes[ $block_type ] ) ?
			$block_bindings_supported_attributes[ $block_type ] :
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
