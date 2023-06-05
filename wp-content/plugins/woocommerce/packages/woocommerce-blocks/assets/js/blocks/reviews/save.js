/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';
import { getBlockClassName, getDataAttrs } from './utils.js';

export default ( { attributes } ) => {
	return (
		<div
			{ ...useBlockProps.save( {
				className: getBlockClassName( attributes ),
			} ) }
			{ ...getDataAttrs( attributes ) }
		/>
	);
};
