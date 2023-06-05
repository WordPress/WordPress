/**
 * External dependencies
 */
import { Icon, category } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import './editor.scss';
import metadata from './block.json';
import { Edit } from './edit';

registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ category }
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
