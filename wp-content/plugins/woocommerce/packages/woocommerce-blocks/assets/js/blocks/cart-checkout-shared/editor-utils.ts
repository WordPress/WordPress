/**
 * External dependencies
 */
import { getBlockTypes } from '@wordpress/blocks';
import { applyCheckoutFilter } from '@woocommerce/blocks-checkout';
import { CART_STORE_KEY } from '@woocommerce/block-data';
import { select } from '@wordpress/data';

// List of core block types to allow in inner block areas.
const coreBlockTypes = [ 'core/paragraph', 'core/image', 'core/separator' ];

/**
 * Gets a list of allowed blocks types under a specific parent block type.
 */
export const getAllowedBlocks = ( block: string ): string[] => {
	const additionalCartCheckoutInnerBlockTypes = applyCheckoutFilter( {
		filterName: 'additionalCartCheckoutInnerBlockTypes',
		defaultValue: [],
		extensions: select( CART_STORE_KEY ).getCartData().extensions,
		arg: { block },
		validation: ( value ) => {
			if (
				Array.isArray( value ) &&
				value.every( ( item ) => typeof item === 'string' )
			) {
				return true;
			}
			throw new Error(
				'allowedBlockTypes filters must return an array of strings.'
			);
		},
	} );

	// Convert to set here so that we remove duplicated block types.
	return Array.from(
		new Set( [
			...getBlockTypes()
				.filter( ( blockType ) =>
					( blockType?.parent || [] ).includes( block )
				)
				.map( ( { name } ) => name ),
			...coreBlockTypes,
			...additionalCartCheckoutInnerBlockTypes,
		] )
	);
};
