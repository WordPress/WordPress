/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Adjust attributes on load to set defaults so default attributes get saved.
 *
 * @param {Object} blockAttributes Original block attributes.
 * @param {Object} blockType       Block type settings.
 *
 * @return {Object} Filtered block attributes.
 */
const setBlockAttributeDefaults = ( blockAttributes, blockType ) => {
	if ( blockType.name.startsWith( 'woocommerce/' ) ) {
		Object.keys( blockType.attributes ).map( ( key ) => {
			if (
				typeof blockAttributes[ key ] === 'undefined' &&
				typeof blockType.defaults !== 'undefined' &&
				typeof blockType.defaults[ key ] !== 'undefined'
			) {
				blockAttributes[ key ] = blockType.defaults[ key ];
			}
			return key;
		} );
	}
	return blockAttributes;
};

/**
 * Hook into `blocks.getBlockAttributes` to set default attributes (if blocks
 * define them separately) when a block is loaded.
 *
 * This is a workaround for Gutenberg which does not save "default" attributes
 * to the post, which means if defaults change, all existing blocks change too.
 *
 * See https://github.com/WordPress/gutenberg/issues/7342
 *
 * To use this, the block name needs a `woocommerce/` prefix, and as well
 * as defining `attributes` during block registration, you must also declare an
 * array called `defaults`. Defaults should be omitted from `attributes`.
 */
addFilter(
	'blocks.getBlockAttributes',
	'woocommerce-blocks/get-block-attributes',
	setBlockAttributeDefaults
);
