/* eslint-disable @wordpress/no-unsafe-wp-apis */

/**
 * External dependencies
 */
import type { BlockAlignment } from '@wordpress/blocks';
import { ProductResponseItem } from '@woocommerce/types';
import { __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles } from '@wordpress/block-editor';
import { Icon, Placeholder, Spinner } from '@wordpress/components';
import classnames from 'classnames';
import { isEmpty } from 'lodash';
import { useCallback, useState } from '@wordpress/element';
import { WP_REST_API_Category } from 'wp-types';
import { useBorderProps } from '@woocommerce/base-hooks';
import type { ComponentType, Dispatch, SetStateAction } from 'react';

/**
 * Internal dependencies
 */
import { CallToAction } from './call-to-action';
import { ConstrainedResizable } from './constrained-resizable';
import { EditorBlock, GenericBlockUIConfig } from './types';
import { useBackgroundImage } from './use-background-image';
import {
	dimRatioToClass,
	getBackgroundImageStyles,
	getClassPrefixFromName,
} from './utils';

interface WithFeaturedItemConfig extends GenericBlockUIConfig {
	emptyMessage: string;
}

export interface FeaturedItemRequiredAttributes {
	contentAlign: BlockAlignment;
	dimRatio: number;
	focalPoint: { x: number; y: number };
	hasParallax: boolean;
	imageFit: 'cover' | 'none';
	isRepeated: boolean;
	linkText: string;
	mediaId: number;
	mediaSrc: string;
	minHeight: number;
	overlayColor: string;
	overlayGradient: string;
	showDesc: boolean;
	showPrice: boolean;
}

interface FeaturedCategoryRequiredAttributes
	extends FeaturedItemRequiredAttributes {
	categoryId: number | 'preview';
	productId: never;
}

interface FeaturedProductRequiredAttributes
	extends FeaturedItemRequiredAttributes {
	categoryId: never;
	productId: number | 'preview';
}

interface FeaturedItemRequiredProps< T > {
	attributes: (
		| FeaturedCategoryRequiredAttributes
		| FeaturedProductRequiredAttributes
	 ) &
		EditorBlock< T >[ 'attributes' ] & {
			// This is hardcoded because border and color are not yet included
			// in Gutenberg's official types.
			style: {
				border?: { radius?: number };
				color?: { text?: string };
			};
			textColor?: string;
		};
	isLoading: boolean;
	setAttributes: ( attrs: Partial< FeaturedItemRequiredAttributes > ) => void;
	useEditingImage: [ boolean, Dispatch< SetStateAction< boolean > > ];
}

interface FeaturedCategoryProps< T > extends FeaturedItemRequiredProps< T > {
	category: WP_REST_API_Category;
	product: never;
}

interface FeaturedProductProps< T > extends FeaturedItemRequiredProps< T > {
	category: never;
	product: ProductResponseItem;
}

type FeaturedItemProps< T extends EditorBlock< T > > =
	| ( T & FeaturedCategoryProps< T > )
	| ( T & FeaturedProductProps< T > );

export const withFeaturedItem =
	( { emptyMessage, icon, label }: WithFeaturedItemConfig ) =>
	< T extends EditorBlock< T > >( Component: ComponentType< T > ) =>
	( props: FeaturedItemProps< T > ) => {
		const [ isEditingImage ] = props.useEditingImage;

		const {
			attributes,
			category,
			isLoading,
			isSelected,
			name,
			product,
			setAttributes,
		} = props;
		const { mediaId, mediaSrc } = attributes;
		const item = category || product;
		const [ backgroundImageSize, setBackgroundImageSize ] = useState( {} );

		const { backgroundImageSrc } = useBackgroundImage( {
			item,
			mediaId,
			mediaSrc,
			blockName: name,
		} );

		const className = getClassPrefixFromName( name );

		const onResize = useCallback(
			( _event, _direction, elt ) => {
				setAttributes( {
					minHeight: parseInt( elt.style.height, 10 ),
				} );
			},
			[ setAttributes ]
		);

		const renderButton = () => {
			const { categoryId, linkText, productId } = attributes;

			return (
				<CallToAction
					itemId={ categoryId || productId }
					linkText={ linkText }
					permalink={ ( category || product ).permalink as string }
				/>
			);
		};

		const renderNoItem = () => (
			<Placeholder
				className={ className }
				icon={ <Icon icon={ icon } /> }
				label={ label }
			>
				{ isLoading ? <Spinner /> : emptyMessage }
			</Placeholder>
		);

		const borderProps = useBorderProps( attributes );

		const renderItem = () => {
			const {
				contentAlign,
				dimRatio,
				focalPoint,
				hasParallax,
				isRepeated,
				imageFit,
				minHeight,
				overlayColor,
				overlayGradient,
				showDesc,
				showPrice,
				style,
				textColor,
			} = attributes;

			const classes = classnames(
				className,
				{
					'is-selected':
						isSelected &&
						attributes.categoryId !== 'preview' &&
						attributes.productId !== 'preview',
					'is-loading': ! item && isLoading,
					'is-not-found': ! item && ! isLoading,
					'has-background-dim': dimRatio !== 0,
					'is-repeated': isRepeated,
				},
				dimRatioToClass( dimRatio ),
				contentAlign !== 'center' && `has-${ contentAlign }-content`
			);

			const containerStyle = {
				borderRadius: style?.border?.radius,
				color: textColor
					? `var(--wp--preset--color--${ textColor })`
					: style?.color?.text,
				boxSizing: 'border-box',
			};

			const wrapperStyle = {
				...getSpacingClassesAndStyles( attributes ).style,
				minHeight,
			};

			const isImgElement = ! isRepeated && ! hasParallax;

			const backgroundImageStyle = getBackgroundImageStyles( {
				focalPoint,
				imageFit,
				isImgElement,
				isRepeated,
				url: backgroundImageSrc,
			} );

			const overlayStyle = {
				background: overlayGradient,
				backgroundColor: overlayColor,
			};

			return (
				<>
					<ConstrainedResizable
						enable={ { bottom: true } }
						onResize={ onResize }
						showHandle={ isSelected }
						style={ { minHeight } }
					/>
					<div
						className={ classes }
						style={ { containerStyle, ...borderProps.style } }
					>
						<div
							className={ `${ className }__wrapper` }
							style={ wrapperStyle }
						>
							<div
								className="background-dim__overlay"
								style={ overlayStyle }
							/>
							{ backgroundImageSrc &&
								( isImgElement ? (
									<img
										alt={ item.name }
										className={ `${ className }__background-image` }
										src={ backgroundImageSrc }
										style={ backgroundImageStyle }
										onLoad={ ( e ) => {
											setBackgroundImageSize( {
												height: e.currentTarget
													?.naturalHeight,
												width: e.currentTarget
													?.naturalWidth,
											} );
										} }
									/>
								) : (
									<div
										className={ classnames(
											`${ className }__background-image`,
											{
												'has-parallax': hasParallax,
											}
										) }
										style={ backgroundImageStyle }
									/>
								) ) }
							<h2
								className={ `${ className }__title` }
								dangerouslySetInnerHTML={ {
									__html: item.name,
								} }
							/>
							{ ! isEmpty( product?.variation ) && (
								<h3
									className={ `${ className }__variation` }
									dangerouslySetInnerHTML={ {
										__html: product.variation,
									} }
								/>
							) }
							{ showDesc && (
								<div
									className={ `${ className }__description` }
									dangerouslySetInnerHTML={ {
										__html:
											category?.description ||
											product?.short_description,
									} }
								/>
							) }
							{ showPrice && (
								<div
									className={ `${ className }__price` }
									dangerouslySetInnerHTML={ {
										__html: product.price_html,
									} }
								/>
							) }
							<div className={ `${ className }__link` }>
								{ renderButton() }
							</div>
						</div>
					</div>
				</>
			);
		};

		if ( isEditingImage ) {
			return (
				<Component
					{ ...props }
					backgroundImageSize={ backgroundImageSize }
				/>
			);
		}

		return (
			<>
				<Component
					{ ...props }
					backgroundImageSize={ backgroundImageSize }
				/>
				{ item ? renderItem() : renderNoItem() }
			</>
		);
	};
