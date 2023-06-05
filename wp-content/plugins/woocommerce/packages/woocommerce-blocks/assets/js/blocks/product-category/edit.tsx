/**
 * External dependencies
 */
import { BlockControls, useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import {
	Disabled,
	ToolbarGroup,
	withSpokenMessages,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ProductByCategoryBlock } from './block';
import { Attributes, Props } from './types';
import { ProductsByCategoryInspectorControls } from './inspector-controls';
import { ProductsByCategoryEditMode } from './edit-mode';

const EditBlock = ( props: Props ): JSX.Element => {
	const blockProps = useBlockProps();

	const { attributes } = props;

	const [ isEditing, setIsEditing ] = useState(
		! attributes.categories.length
	);

	const [ changedAttributes, setChangedAttributes ] = useState<
		Partial< Attributes >
	>( {} );

	return (
		<div { ...blockProps }>
			<BlockControls>
				<ToolbarGroup
					controls={ [
						{
							icon: 'edit',
							title: __(
								'Edit selected categories',
								'woo-gutenberg-products-block'
							),
							onClick: () => setIsEditing( ! isEditing ),
							isActive: isEditing,
						},
					] }
				/>
			</BlockControls>
			<ProductsByCategoryInspectorControls
				isEditing={ isEditing }
				setChangedAttributes={ setChangedAttributes }
				{ ...props }
			/>
			{ isEditing ? (
				<ProductsByCategoryEditMode
					isEditing={ isEditing }
					setIsEditing={ setIsEditing }
					changedAttributes={ changedAttributes }
					setChangedAttributes={ setChangedAttributes }
					{ ...props }
				/>
			) : (
				<Disabled>
					<ProductByCategoryBlock { ...props } />
				</Disabled>
			) }
		</div>
	);
};

export const Edit = withSpokenMessages( EditBlock );
