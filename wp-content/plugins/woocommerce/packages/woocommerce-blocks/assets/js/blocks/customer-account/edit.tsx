/**
 * External dependencies
 */
import classNames from 'classnames';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import type { BlockEditProps } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Block from './block';
import { Attributes } from './types';
import { BlockSettings } from './sidebar-settings';
import './editor.scss';

const Edit = ( {
	attributes,
	setAttributes,
}: BlockEditProps< Attributes > ) => {
	const { className } = attributes;
	const blockProps = useBlockProps( {
		className: classNames( 'wc-block-editor-customer-account', className ),
	} );

	return (
		<>
			<div { ...blockProps }>
				<InspectorControls>
					<BlockSettings
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
				</InspectorControls>
				<Disabled>
					<Block attributes={ attributes } />
				</Disabled>
			</div>
		</>
	);
};

export default Edit;
