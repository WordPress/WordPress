/**
 * External dependencies
 */
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { getBlockClassName } from '../utils.js';

const { attributes: attributeDefinitions } = metadata;

const v1 = {
	attributes: Object.assign( {}, attributeDefinitions, {
		rows: { type: 'number', default: 1 },
	} ),
	save( { attributes } ) {
		const data = {
			'data-attributes': JSON.stringify( attributes ),
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
	},
};

export default [ v1 ];
