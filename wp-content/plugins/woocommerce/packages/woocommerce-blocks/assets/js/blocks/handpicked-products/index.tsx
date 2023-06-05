/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { getSetting } from '@woocommerce/settings';
import { Icon, stack } from '@wordpress/icons';

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
				icon={ stack }
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
	},

	edit: Edit,

	save: () => {
		return null;
	},
} );
