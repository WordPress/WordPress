/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	AlignmentToolbar,
	BlockControls as BlockControlsWrapper,
	MediaReplaceFlow,
} from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { crop } from '@wordpress/icons';
import { WP_REST_API_Category } from 'wp-types';
import { ProductResponseItem } from '@woocommerce/types';
import TextToolbarButton from '@woocommerce/editor-components/text-toolbar-button';
import type { ComponentType, Dispatch, SetStateAction } from 'react';
import type { BlockAlignment } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { useBackgroundImage } from './use-background-image';
import { EditorBlock, GenericBlockUIConfig } from './types';

type Media = { id: number; url: string };

interface WithBlockControlsRequiredProps< T > {
	attributes: BlockControlRequiredAttributes &
		EditorBlock< T >[ 'attributes' ];
	setAttributes: ( attrs: Partial< BlockControlRequiredAttributes > ) => void;
	useEditingImage: [ boolean, Dispatch< SetStateAction< boolean > > ];
}

interface WithBlockControlsCategoryProps< T >
	extends WithBlockControlsRequiredProps< T > {
	category: WP_REST_API_Category;
	product: never;
}

interface WithBlockControlsProductProps< T >
	extends WithBlockControlsRequiredProps< T > {
	category: never;
	product: ProductResponseItem;
}

type WithBlockControlsProps< T extends EditorBlock< T > > =
	| ( T & WithBlockControlsCategoryProps< T > )
	| ( T & WithBlockControlsProductProps< T > );

type BlockControlRequiredAttributes = {
	contentAlign: BlockAlignment;
	editMode: boolean;
	mediaId: number;
	mediaSrc: string;
};

interface BlockControlsProps {
	backgroundImageId: number;
	backgroundImageSrc: string;
	contentAlign: BlockAlignment;
	cropLabel: string;
	editLabel: string;
	editMode: boolean;
	isEditingImage: boolean;
	mediaSrc: string;
	setAttributes: ( attrs: Partial< BlockControlRequiredAttributes > ) => void;
	setIsEditingImage: ( value: boolean ) => void;
}

interface BlockControlsConfiguration extends GenericBlockUIConfig {
	cropLabel: string;
	editLabel: string;
}

export const BlockControls = ( {
	backgroundImageId,
	backgroundImageSrc,
	contentAlign,
	cropLabel,
	editLabel,
	editMode,
	isEditingImage,
	mediaSrc,
	setAttributes,
	setIsEditingImage,
}: BlockControlsProps ) => {
	return (
		<BlockControlsWrapper>
			<AlignmentToolbar
				value={ contentAlign }
				onChange={ ( nextAlign: BlockAlignment ) => {
					setAttributes( { contentAlign: nextAlign } );
				} }
			/>
			<ToolbarGroup>
				{ backgroundImageSrc && ! isEditingImage && (
					<ToolbarButton
						onClick={ () => setIsEditingImage( true ) }
						icon={ crop }
						label={ cropLabel }
					/>
				) }
				<MediaReplaceFlow
					mediaId={ backgroundImageId }
					mediaURL={ mediaSrc }
					accept="image/*"
					onSelect={ ( media: Media ) => {
						setAttributes( {
							mediaId: media.id,
							mediaSrc: media.url,
						} );
					} }
					allowedTypes={ [ 'image' ] }
				/>
				{ backgroundImageId && mediaSrc ? (
					<TextToolbarButton
						onClick={ () =>
							setAttributes( { mediaId: 0, mediaSrc: '' } )
						}
					>
						{ __( 'Reset', 'woo-gutenberg-products-block' ) }
					</TextToolbarButton>
				) : null }
			</ToolbarGroup>
			<ToolbarGroup
				controls={ [
					{
						icon: 'edit',
						title: editLabel,
						onClick: () =>
							setAttributes( { editMode: ! editMode } ),
						isActive: editMode,
					},
				] }
			/>
		</BlockControlsWrapper>
	);
};

export const withBlockControls =
	( { cropLabel, editLabel }: BlockControlsConfiguration ) =>
	< T extends EditorBlock< T > >( Component: ComponentType< T > ) =>
	( props: WithBlockControlsProps< T > ) => {
		const [ isEditingImage, setIsEditingImage ] = props.useEditingImage;
		const { attributes, category, name, product, setAttributes } = props;
		const { contentAlign, editMode, mediaId, mediaSrc } = attributes;
		const item = category || product;

		const { backgroundImageId, backgroundImageSrc } = useBackgroundImage( {
			item,
			mediaId,
			mediaSrc,
			blockName: name,
		} );

		return (
			<>
				<BlockControls
					backgroundImageId={ backgroundImageId }
					backgroundImageSrc={ backgroundImageSrc }
					contentAlign={ contentAlign }
					cropLabel={ cropLabel }
					editLabel={ editLabel }
					editMode={ editMode }
					isEditingImage={ isEditingImage }
					mediaSrc={ mediaSrc }
					setAttributes={ setAttributes }
					setIsEditingImage={ setIsEditingImage }
				/>
				<Component { ...props } />
			</>
		);
	};
