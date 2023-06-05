/**
 * External dependencies
 */
import classnames from 'classnames';
import ProductPrice from '@woocommerce/base-components/product-price';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import {
	useInnerBlockLayoutContext,
	useProductDataContext,
} from '@woocommerce/shared-context';
import {
	useColorProps,
	useSpacingProps,
	useTypographyProps,
} from '@woocommerce/base-hooks';
import { withProductDataContext } from '@woocommerce/shared-hocs';
import { CurrencyCode } from '@woocommerce/type-defs/currency';
import type { HTMLAttributes } from 'react';

/**
 * Internal dependencies
 */
import type { BlockAttributes } from './types';
import './style.scss';

type Props = BlockAttributes & HTMLAttributes< HTMLDivElement >;

interface PriceProps {
	currency_code: CurrencyCode;
	currency_symbol: string;
	currency_minor_unit: number;
	currency_decimal_separator: string;
	currency_thousand_separator: string;
	currency_prefix: string;
	currency_suffix: string;
	price: string;
	regular_price: string;
	sale_price: string;
	price_range: null | { min_amount: string; max_amount: string };
}

export const Block = ( props: Props ): JSX.Element | null => {
	const { className, textAlign, isDescendentOfSingleProductTemplate } = props;
	const { parentClassName } = useInnerBlockLayoutContext();
	const { product } = useProductDataContext();

	const colorProps = useColorProps( props );
	const spacingProps = useSpacingProps( props );
	const typographyProps = useTypographyProps( props );

	const wrapperClassName = classnames(
		'wc-block-components-product-price',
		className,
		colorProps.className,
		{
			[ `${ parentClassName }__product-price` ]: parentClassName,
		},
		typographyProps.className
	);

	if ( ! product.id && ! isDescendentOfSingleProductTemplate ) {
		return (
			<ProductPrice align={ textAlign } className={ wrapperClassName } />
		);
	}

	const style = {
		...colorProps.style,
		...typographyProps.style,
	};
	const spacingStyle = {
		...spacingProps.style,
	};
	const prices: PriceProps = product.prices;
	const currency = isDescendentOfSingleProductTemplate
		? getCurrencyFromPriceResponse()
		: getCurrencyFromPriceResponse( prices );

	const pricePreview = '5000';
	const isOnSale = prices.price !== prices.regular_price;
	const priceClassName = classnames( {
		[ `${ parentClassName }__product-price__value` ]: parentClassName,
		[ `${ parentClassName }__product-price__value--on-sale` ]: isOnSale,
	} );

	return (
		<ProductPrice
			align={ textAlign }
			className={ wrapperClassName }
			regularPriceStyle={ style }
			priceStyle={ style }
			priceClassName={ priceClassName }
			currency={ currency }
			price={
				isDescendentOfSingleProductTemplate
					? pricePreview
					: prices.price
			}
			// Range price props
			minPrice={ prices?.price_range?.min_amount }
			maxPrice={ prices?.price_range?.max_amount }
			// This is the regular or original price when the `price` value is a sale price.
			regularPrice={
				isDescendentOfSingleProductTemplate
					? pricePreview
					: prices.regular_price
			}
			regularPriceClassName={ classnames( {
				[ `${ parentClassName }__product-price__regular` ]:
					parentClassName,
			} ) }
			spacingStyle={ spacingStyle }
		/>
	);
};

export default ( props: Props ) => {
	// It is necessary because this block has to support serveral contexts:
	// - Inside `All Products Block` -> `withProductDataContext` HOC
	// - Inside `Products Block` -> Gutenberg Context
	// - Inside `Single Product Template` -> Gutenberg Context
	// - Without any parent -> `WithSelector` and `withProductDataContext` HOCs
	// For more details, check https://github.com/woocommerce/woocommerce-blocks/pull/8609
	if ( props.isDescendentOfSingleProductTemplate ) {
		return <Block { ...props } />;
	}
	return withProductDataContext( Block )( props );
};
