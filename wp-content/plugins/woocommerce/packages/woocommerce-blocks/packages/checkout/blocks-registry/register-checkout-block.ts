/**
 * External dependencies
 */
import { registerBlockComponent } from '@woocommerce/blocks-registry';

/**
 * Internal dependencies
 */
import {
	assertBlockName,
	assertBlockParent,
	assertOption,
	assertBlockComponent,
} from './utils';
import { registeredBlocks } from './registered-blocks';
import type { CheckoutBlockOptions } from './types';

/**
 * Main API for registering a new checkout block within areas.
 */
export const registerCheckoutBlock = (
	options: CheckoutBlockOptions
): void => {
	assertOption( options, 'metadata', 'object' );
	assertBlockName( options.metadata.name );
	assertBlockParent( options.metadata.parent );
	assertBlockComponent( options, 'component' );

	/**
	 * This ensures the frontend component for the checkout block is available.
	 */
	registerBlockComponent( {
		blockName: options.metadata.name as string,
		component: options.component,
	} );

	// Infer the `force` value from whether the block is locked or not. But
	// allow overriding it on block registration.
	const force =
		typeof options.force === 'boolean'
			? options.force
			: Boolean( options.metadata?.attributes?.lock?.default?.remove );

	/**
	 * Store block metadata for later lookup.
	 */
	registeredBlocks[ options.metadata.name ] = {
		blockName: options.metadata.name,
		metadata: options.metadata,
		component: options.component,
		force,
	};
};
