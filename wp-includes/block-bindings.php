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
 * Sources are used to override block's original attributes with a value
 * coming from the source. Once a source is registered, it can be used by a
 * block by setting its `metadata.bindings` attribute to a value that refers
 * to the source.
 *
 * @since 6.5.0
 *
 * @param string   $source_name       The name of the source.
 * @param array    $source_properties {
 *     The array of arguments that are used to register a source.
 *
 *     @type string   $label              The label of the source.
 *     @type callback $get_value_callback A callback executed when the source is processed during block rendering.
 *                                        The callback should have the following signature:
 *
 *                                        `function ($source_args, $block_instance,$attribute_name): mixed`
 *                                            - @param array    $source_args    Array containing source arguments
 *                                                                              used to look up the override value,
 *                                                                              i.e. {"key": "foo"}.
 *                                            - @param WP_Block $block_instance The block instance.
 *                                            - @param string   $attribute_name The name of an attribute .
 *                                        The callback has a mixed return type; it may return a string to override
 *                                        the block's original value, null, false to remove an attribute, etc.
 * }
 * @return array|false Source when the registration was successful, or `false` on failure.
 */
function register_block_bindings_source( $source_name, array $source_properties ) {
	return WP_Block_Bindings_Registry::get_instance()->register( $source_name, $source_properties );
}

/**
 * Unregisters a block bindings source.
 *
 * @since 6.5.0
 *
 * @param string $source_name Block bindings source name including namespace.
 * @return array|false The unregistred block bindings source on success and `false` otherwise.
 */
function unregister_block_bindings_source( $source_name ) {
	return WP_Block_Bindings_Registry::get_instance()->unregister( $source_name );
}

/**
 * Retrieves the list of all registered block bindings sources.
 *
 * @since 6.5.0
 *
 * @return array The array of registered block bindings sources.
 */
function get_all_registered_block_bindings_sources() {
	return WP_Block_Bindings_Registry::get_instance()->get_all_registered();
}
