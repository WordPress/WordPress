/**
 * External dependencies
 */
import { folderStarred } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';
import './editor.scss';
import Block from './block';
import metadata from './block.json';
import { register } from '../register';
import { example } from './example';

register( Block, example, metadata, {
	icon: {
		src: (
			<Icon
				icon={ folderStarred }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
} );
