/**
 * External dependencies
 */
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { getBlockClassName } from '../utils.js';

export default function save( { attributes } ) {
	const dataAttributes = {};
	Object.keys( attributes )
		.sort()
		.forEach( ( key ) => {
			dataAttributes[ key ] = attributes[ key ];
		} );
	const data = {
		'data-attributes': JSON.stringify( dataAttributes ),
	};
	return (
		<div
			className={ getBlockClassName(
				'wc-block-all-products',
				attributes
			) }
			{ ...data }
		>
			<InnerBlocks.Content />
		</div>
	);
}
