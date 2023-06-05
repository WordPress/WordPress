/* eslint-disable @wordpress/no-unsafe-wp-apis */

/**
 * External dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';
import { WP_REST_API_Category } from 'wp-types';
import { ProductResponseItem } from '@woocommerce/types';
import {
	__experimentalImageEditingProvider as ImageEditingProvider,
	__experimentalImageEditor as GutenbergImageEditor,
} from '@wordpress/block-editor';
import type { ComponentType, Dispatch, SetStateAction } from 'react';

/**
 * Internal dependencies
 */
import { BLOCK_NAMES, DEFAULT_EDITOR_SIZE } from './constants';
import { EditorBlock } from './types';
import { useBackgroundImage } from './use-background-image';

type MediaAttributes = { align: string; mediaId: number; mediaSrc: string };
type MediaSize = { height: number; width: number };

interface WithImageEditorRequiredProps< T > {
	attributes: MediaAttributes & EditorBlock< T >[ 'attributes' ];
	backgroundImageSize: MediaSize;
	setAttributes: ( attrs: Partial< MediaAttributes > ) => void;
	useEditingImage: [ boolean, Dispatch< SetStateAction< boolean > > ];
}

interface WithImageEditorCategoryProps< T >
	extends WithImageEditorRequiredProps< T > {
	category: WP_REST_API_Category;
	product: never;
}

interface WithImageEditorProductProps< T >
	extends WithImageEditorRequiredProps< T > {
	category: never;
	product: ProductResponseItem;
}

type WithImageEditorProps< T extends EditorBlock< T > > =
	| ( T & WithImageEditorCategoryProps< T > )
	| ( T & WithImageEditorProductProps< T > );

interface ImageEditorProps {
	align: string;
	backgroundImageId: number;
	backgroundImageSize: MediaSize;
	backgroundImageSrc: string;
	containerRef: React.RefObject< HTMLDivElement >;
	isEditingImage: boolean;
	setAttributes: ( attrs: MediaAttributes ) => void;
	setIsEditingImage: ( value: boolean ) => void;
}

// Adapted from:
// https://github.com/WordPress/gutenberg/blob/v15.6.1/packages/block-library/src/image/use-client-width.js
function useClientWidth(
	ref: React.RefObject< HTMLDivElement >,
	dependencies: string[]
) {
	const [ clientWidth, setClientWidth ]: [
		number | undefined,
		Dispatch< SetStateAction< number | undefined > >
	] = useState();

	const calculateClientWidth = useCallback( () => {
		setClientWidth( ref.current?.clientWidth );
	}, [ ref ] );

	useEffect( calculateClientWidth, [
		calculateClientWidth,
		...dependencies,
	] );
	useEffect( () => {
		if ( ! ref.current ) {
			return;
		}
		const { defaultView } = ref.current.ownerDocument;

		if ( ! defaultView ) {
			return;
		}
		defaultView.addEventListener( 'resize', calculateClientWidth );

		return () => {
			defaultView.removeEventListener( 'resize', calculateClientWidth );
		};
	}, [ ref, calculateClientWidth ] );

	return clientWidth;
}

export const ImageEditor = ( {
	align,
	backgroundImageId,
	backgroundImageSize,
	backgroundImageSrc,
	containerRef,
	isEditingImage,
	setAttributes,
	setIsEditingImage,
}: ImageEditorProps ) => {
	const clientWidth = useClientWidth( containerRef, [ align ] );

	// Fallback for WP 6.1 or lower. In WP 6.2. ImageEditingProvider was merged
	// with ImageEditor, see: https://github.com/WordPress/gutenberg/pull/47171
	if ( typeof ImageEditingProvider === 'function' ) {
		return (
			<ImageEditingProvider
				id={ backgroundImageId }
				url={ backgroundImageSrc }
				naturalHeight={
					backgroundImageSize.height || DEFAULT_EDITOR_SIZE.height
				}
				naturalWidth={
					backgroundImageSize.width || DEFAULT_EDITOR_SIZE.width
				}
				onSaveImage={ ( { id, url }: { id: number; url: string } ) => {
					setAttributes( { mediaId: id, mediaSrc: url } );
				} }
				isEditing={ isEditingImage }
				onFinishEditing={ () => setIsEditingImage( false ) }
			>
				<GutenbergImageEditor
					url={ backgroundImageSrc }
					height={
						backgroundImageSize.height || DEFAULT_EDITOR_SIZE.height
					}
					width={
						backgroundImageSize.width || DEFAULT_EDITOR_SIZE.width
					}
				/>
			</ImageEditingProvider>
		);
	}

	return (
		<GutenbergImageEditor
			id={ backgroundImageId }
			url={ backgroundImageSrc }
			height={ backgroundImageSize.height || DEFAULT_EDITOR_SIZE.height }
			width={ backgroundImageSize.width || DEFAULT_EDITOR_SIZE.width }
			naturalHeight={ backgroundImageSize.height }
			naturalWidth={ backgroundImageSize.width }
			onSaveImage={ ( { id, url }: { id: number; url: string } ) => {
				setAttributes( { mediaId: id, mediaSrc: url } );
			} }
			onFinishEditing={ () => setIsEditingImage( false ) }
			clientWidth={ clientWidth }
		/>
	);
};

export const withImageEditor =
	< T extends EditorBlock< T > >( Component: ComponentType< T > ) =>
	( props: WithImageEditorProps< T > ) => {
		const [ isEditingImage, setIsEditingImage ] = props.useEditingImage;

		const ref = useRef< HTMLDivElement >( null );

		const { attributes, backgroundImageSize, name, setAttributes } = props;
		const { mediaId, mediaSrc } = attributes;
		const item =
			name === BLOCK_NAMES.featuredProduct
				? props.product
				: props.category;

		const { backgroundImageId, backgroundImageSrc } = useBackgroundImage( {
			item,
			mediaId,
			mediaSrc,
			blockName: name,
		} );

		if ( isEditingImage ) {
			return (
				<div ref={ ref }>
					<ImageEditor
						align={ attributes.align }
						backgroundImageId={ backgroundImageId }
						backgroundImageSize={ backgroundImageSize }
						backgroundImageSrc={ backgroundImageSrc }
						containerRef={ ref }
						isEditingImage={ isEditingImage }
						setAttributes={ setAttributes }
						setIsEditingImage={ setIsEditingImage }
					/>
				</div>
			);
		}

		return <Component { ...props } />;
	};
