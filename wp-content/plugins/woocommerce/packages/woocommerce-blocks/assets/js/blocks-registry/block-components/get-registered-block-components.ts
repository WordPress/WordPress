/**
 * External dependencies
 */
import deprecated from '@wordpress/deprecated';
import type { RegisteredBlockComponent } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { registeredBlockComponents } from './registered-block-components-init';

/**
 * Get all Registered Block Components.
 *
 * WooCommerce Blocks allows React Components to be used on the frontend of the store in place of
 * Blocks instead of just serving static content.
 *
 * This gets all registered Block Components so we know which Blocks map to which React Components.
 *
 * @param {string} context Current context (a named parent Block). If Block Components were only
 *                         registered under a certain context, those Components will be returned,
 *                         as well as any Components registered under all contexts.
 * @return {Object} List of React Components registered under the provided context.
 */
export function getRegisteredBlockComponents(
	context: string
): Record< string, RegisteredBlockComponent > {
	const parentInnerBlocks =
		typeof registeredBlockComponents[ context ] === 'object' &&
		Object.keys( registeredBlockComponents[ context ] ).length > 0
			? registeredBlockComponents[ context ]
			: {};

	return {
		...parentInnerBlocks,
		...registeredBlockComponents.any,
	};
}

/**
 * Alias of getRegisteredBlockComponents kept for backwards compatibility.
 *
 * @param {string} main Name of the parent block to retrieve children of.
 * @return {Object} List of registered inner blocks.
 */
export function getRegisteredInnerBlocks(
	main: string
): Record< string, RegisteredBlockComponent > {
	deprecated( 'getRegisteredInnerBlocks', {
		version: '2.8.0',
		alternative: 'getRegisteredBlockComponents',
		plugin: 'WooCommerce Blocks',
	} );
	return getRegisteredBlockComponents( main );
}
