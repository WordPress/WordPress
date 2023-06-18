/**
 * External dependencies
 */
import { Fragment } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import classnames from 'classnames';
import { PLACEHOLDER_IMG_SRC } from '@woocommerce/settings';
import {
	useInnerBlockLayoutContext,
	useProductDataContext,
} from '@woocommerce/shared-context';
import { useStyleProps } from '@woocommerce/base-hooks';
import { withProductDataContext } from '@woocommerce/shared-hocs';
import { useStoreEvents } from '@woocommerce/base-context/hooks';
import type { HTMLAttributes } from 'react';

/**
 * Internal dependencies
 */
import ProductSaleBadge from '../sale-badge/block';
import './style.scss';
import { BlockAttributes, ImageSizing } from './types';

const ImagePlaceholder = (): JSX.Element => {
	return (
		<img
			src={ PLACEHOLDER_IMG_SRC }
			alt=""
			width={ undefined }
			height={ undefined }
		/>
	);
};

interface ImageProps {
	image?: null | {
		alt?: string | undefined;
		id: number;
		name: string;
		sizes?: string | undefined;
		src?: string | undefined;
		srcset?: string | undefined;
		thumbnail?: string | undefined;
	};
	loaded: boolean;
	showFullSize: boolean;
	fallbackAlt: string;
}

const Image = ( {
	image,
	loaded,
	showFullSize,
	fallbackAlt,
}: ImageProps ): JSX.Element => {
	const { thumbnail, src, srcset, sizes, alt } = image || {};
	const imageProps = {
		alt: alt || fallbackAlt,
		hidden: ! loaded,
		src: thumbnail,
		...( showFullSize && { src, srcSet: srcset, sizes } ),
	};

	return (
		<>
			{ imageProps.src && (
				/* eslint-disable-next-line jsx-a11y/alt-text */
				<img data-testid="product-image" { ...imageProps } />
			) }
			{ ! image && <ImagePlaceholder /> }
		</>
	);
};

type Props = BlockAttributes & HTMLAttributes< HTMLDivElement >;

export const Block = ( props: Props ): JSX.Element | null => {
	const {
		className,
		imageSizing = ImageSizing.SINGLE,
		showProductLink = true,
		showSaleBadge,
		saleBadgeAlign = 'right',
		...restProps
	} = props;
	const styleProps = useStyleProps( props );
	const { parentClassName } = useInnerBlockLayoutContext();
	const { product, isLoading } = useProductDataContext();
	const { dispatchStoreEvent } = useStoreEvents();

	if ( ! product.id ) {
		return (
			<div
				className={ classnames(
					className,
					'wc-block-components-product-image',
					{
						[ `${ parentClassName }__product-image` ]:
							parentClassName,
					},
					styleProps.className
				) }
				style={ styleProps.style }
			>
				<ImagePlaceholder />
			</div>
		);
	}
	const hasProductImages = !! product.images.length;
	const image = hasProductImages ? product.images[ 0 ] : null;
	const ParentComponent = showProductLink ? 'a' : Fragment;
	const anchorLabel = sprintf(
		/* translators: %s is referring to the product name */
		__( 'Link to %s', 'woo-gutenberg-products-block' ),
		product.name
	);
	const anchorProps = {
		href: product.permalink,
		...( ! hasProductImages && { 'aria-label': anchorLabel } ),
		onClick: () => {
			dispatchStoreEvent( 'product-view-link', {
				product,
			} );
		},
	};

	return (
		<div
			className={ classnames(
				className,
				'wc-block-components-product-image',
				{
					[ `${ parentClassName }__product-image` ]: parentClassName,
				},
				styleProps.className
			) }
			style={ styleProps.style }
		>
			<ParentComponent { ...( showProductLink && anchorProps ) }>
				{ !! showSaleBadge && (
					<ProductSaleBadge
						align={ saleBadgeAlign }
						{ ...restProps }
					/>
				) }
				<Image
					fallbackAlt={ product.name }
					image={ image }
					loaded={ ! isLoading }
					showFullSize={ imageSizing !== ImageSizing.THUMBNAIL }
				/>
			</ParentComponent>
		</div>
	);
};

export default withProductDataContext( Block );
