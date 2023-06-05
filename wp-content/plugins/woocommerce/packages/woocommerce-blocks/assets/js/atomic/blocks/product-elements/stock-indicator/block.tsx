/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import classnames from 'classnames';
import {
	useInnerBlockLayoutContext,
	useProductDataContext,
} from '@woocommerce/shared-context';
import { useColorProps, useTypographyProps } from '@woocommerce/base-hooks';
import { withProductDataContext } from '@woocommerce/shared-hocs';
import type { HTMLAttributes } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';
import type { BlockAttributes } from './types';

const lowStockText = ( lowStock: number ): string => {
	return sprintf(
		/* translators: %d stock amount (number of items in stock for product) */
		__( '%d left in stock', 'woo-gutenberg-products-block' ),
		lowStock
	);
};

const stockText = ( inStock: boolean, isBackordered: boolean ): string => {
	if ( isBackordered ) {
		return __( 'Available on backorder', 'woo-gutenberg-products-block' );
	}

	return inStock
		? __( 'In Stock', 'woo-gutenberg-products-block' )
		: __( 'Out of Stock', 'woo-gutenberg-products-block' );
};

type Props = BlockAttributes & HTMLAttributes< HTMLDivElement >;

export const Block = ( props: Props ): JSX.Element | null => {
	const { className } = props;
	const { parentClassName } = useInnerBlockLayoutContext();
	const { product } = useProductDataContext();
	const colorProps = useColorProps( props );
	const typographyProps = useTypographyProps( props );

	if ( ! product.id || ! product.is_purchasable ) {
		return null;
	}

	const inStock = !! product.is_in_stock;
	const lowStock = product.low_stock_remaining;
	const isBackordered = product.is_on_backorder;

	return (
		<div
			className={ classnames(
				className,
				colorProps.className,
				'wc-block-components-product-stock-indicator',
				{
					[ `${ parentClassName }__stock-indicator` ]:
						parentClassName,
					'wc-block-components-product-stock-indicator--in-stock':
						inStock,
					'wc-block-components-product-stock-indicator--out-of-stock':
						! inStock,
					'wc-block-components-product-stock-indicator--low-stock':
						!! lowStock,
					'wc-block-components-product-stock-indicator--available-on-backorder':
						!! isBackordered,
				}
			) }
			style={ { ...colorProps.style, ...typographyProps.style } }
		>
			{ lowStock
				? lowStockText( lowStock )
				: stockText( inStock, isBackordered ) }
		</div>
	);
};

export default withProductDataContext( Block );
