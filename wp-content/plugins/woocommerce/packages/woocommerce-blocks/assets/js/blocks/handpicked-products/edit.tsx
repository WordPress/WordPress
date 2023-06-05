/**
 * External dependencies
 */
import { BlockControls, useBlockProps } from '@wordpress/block-editor';
import {
	ToolbarGroup,
	Disabled,
	withSpokenMessages,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import './editor.scss';
import { Props } from './types';
import { HandpickedProductsInspectorControls } from './inspector-controls';
import { HandpickedProductsEditMode } from './edit-mode';
import { HandpickedProductsBlock } from './block';

export const EditBlock = ( props: Props ): JSX.Element => {
	const blockProps = useBlockProps();

	const {
		attributes: { products },
	} = props;

	const [ isEditing, setIsEditing ] = useState( ! products.length );

	return (
		<div { ...blockProps }>
			<BlockControls>
				<ToolbarGroup
					controls={ [
						{
							icon: 'edit',
							title: __(
								'Edit selected products',
								'woo-gutenberg-products-block'
							),
							onClick: () => setIsEditing( ! isEditing ),
							isActive: isEditing,
						},
					] }
				/>
			</BlockControls>
			<HandpickedProductsInspectorControls { ...props } />
			{ isEditing ? (
				<HandpickedProductsEditMode
					isEditing={ isEditing }
					setIsEditing={ setIsEditing }
					{ ...props }
				/>
			) : (
				<Disabled>
					<HandpickedProductsBlock { ...props } />
				</Disabled>
			) }
		</div>
	);
};

export const Edit = withSpokenMessages( EditBlock );
