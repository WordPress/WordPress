/**
 * External dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import type { ComponentType } from 'react';

/**
 * Internal dependencies
 */
import { EditorBlock } from './types';

interface EditingImageRequiredProps {
	isSelected: boolean;
}

type EditingImageProps< T extends EditorBlock< T > > = T &
	EditingImageRequiredProps;

export const withEditingImage =
	< T extends EditorBlock< T > >( Component: ComponentType< T > ) =>
	( props: EditingImageProps< T > ) => {
		const [ isEditingImage, setIsEditingImage ] = useState( false );
		const { isSelected } = props;

		useEffect( () => {
			setIsEditingImage( false );
		}, [ isSelected ] );

		return (
			<Component
				{ ...props }
				useEditingImage={ [ isEditingImage, setIsEditingImage ] }
			/>
		);
	};
