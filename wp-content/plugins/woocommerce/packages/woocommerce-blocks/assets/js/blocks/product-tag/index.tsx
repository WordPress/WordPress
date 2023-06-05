/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { getSetting } from '@woocommerce/settings';
import { Icon, tag } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './editor.scss';
import metadata from './block.json';
import { Edit } from './edit';

/**
 * Register and run the "Products by Tag" block.
 */
registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ tag }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	attributes: {
		...metadata.attributes,
		columns: {
			type: 'number',
			default: getSetting( 'default_columns', 3 ),
		},
		rows: {
			type: 'number',
			default: getSetting( 'default_rows', 3 ),
		},
		tags: {
			type: 'array',
			default: [],
		},
		stockStatus: {
			type: 'array',
			default: Object.keys( getSetting( 'stockStatusOptions', [] ) ),
		},
	},

	edit: Edit,

	save: () => {
		return null;
	},
} );
