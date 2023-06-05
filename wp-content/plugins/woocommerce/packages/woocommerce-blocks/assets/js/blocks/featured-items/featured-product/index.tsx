/**
 * External dependencies
 */
import { Icon, starEmpty } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';
import './editor.scss';
import Block from './block';
import { register } from '../register';
import { example } from './example';
import metadata from './block.json';

register( Block, example, metadata, {
	icon: {
		src: (
			<Icon
				icon={ starEmpty }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
} );
