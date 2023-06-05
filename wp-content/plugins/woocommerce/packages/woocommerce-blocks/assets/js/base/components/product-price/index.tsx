/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import FormattedMonetaryAmount from '@woocommerce/base-components/formatted-monetary-amount';
import classNames from 'classnames';
import { formatPrice } from '@woocommerce/price-format';
import { createInterpolateElement } from '@wordpress/element';
import type { Currency } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import './style.scss';

interface PriceRangeProps {
	/**
	 * Currency configuration object
	 */
	currency: Currency | Record< string, never > | undefined;
	/**
	 * The maximum price for the range
	 */
	maxPrice: string | number;
	/**
	 * The minimum price for the range
	 */
	minPrice: string | number;
	/**
	 * CSS class applied to each of the elements containing the prices
	 *
	 * **Note:** this excludes the dash in between the elements
	 */
	priceClassName?: string | undefined;
	/**
	 * Any custom style to be applied to each of the elements containing the prices
	 *
	 * **Note:** this excludes the dash in between the elements
	 */
	priceStyle?: React.CSSProperties | undefined;
}

const PriceRange = ( {
	currency,
	maxPrice,
	minPrice,
	priceClassName,
	priceStyle = {},
}: PriceRangeProps ) => {
	return (
		<>
			<span className="screen-reader-text">
				{ sprintf(
					/* translators: %1$s min price, %2$s max price */
					__(
						'Price between %1$s and %2$s',
						'woo-gutenberg-products-block'
					),
					formatPrice( minPrice ),
					formatPrice( maxPrice )
				) }
			</span>
			<span aria-hidden={ true }>
				<FormattedMonetaryAmount
					className={ classNames(
						'wc-block-components-product-price__value',
						priceClassName
					) }
					currency={ currency }
					value={ minPrice }
					style={ priceStyle }
				/>
				&nbsp;&mdash;&nbsp;
				<FormattedMonetaryAmount
					className={ classNames(
						'wc-block-components-product-price__value',
						priceClassName
					) }
					currency={ currency }
					value={ maxPrice }
					style={ priceStyle }
				/>
			</span>
		</>
	);
};

interface SalePriceProps {
	/**
	 * Currency configuration object
	 */
	currency: Currency | Record< string, never > | undefined;
	/**
	 * CSS class to be applied to the regular price container
	 *
	 * i.e. `<del>` element
	 */
	regularPriceClassName?: string | undefined;
	/**
	 * Custom style to be applied to the regular price container
	 *
	 * i.e. `<del>` element
	 */
	regularPriceStyle?: React.CSSProperties | undefined;
	/**
	 * The regular price before the sale
	 */
	regularPrice: number | string;
	/**
	 * CSS class to be applied to the sale price container
	 *
	 * i.e. `<ins>` element
	 */
	priceClassName?: string | undefined;
	/**
	 * Custom style to be applied to the regular price container
	 *
	 * i.e. `<ins>` element
	 */
	priceStyle?: React.CSSProperties | undefined;
	/**
	 * The new price during the sale
	 */
	price: number | string | undefined;
}

const SalePrice = ( {
	currency,
	regularPriceClassName,
	regularPriceStyle,
	regularPrice,
	priceClassName,
	priceStyle,
	price,
}: SalePriceProps ) => {
	return (
		<>
			<span className="screen-reader-text">
				{ __( 'Previous price:', 'woo-gutenberg-products-block' ) }
			</span>
			<FormattedMonetaryAmount
				currency={ currency }
				renderText={ ( value ) => (
					<del
						className={ classNames(
							'wc-block-components-product-price__regular',
							regularPriceClassName
						) }
						style={ regularPriceStyle }
					>
						{ value }
					</del>
				) }
				value={ regularPrice }
			/>
			<span className="screen-reader-text">
				{ __( 'Discounted price:', 'woo-gutenberg-products-block' ) }
			</span>
			<FormattedMonetaryAmount
				currency={ currency }
				renderText={ ( value ) => (
					<ins
						className={ classNames(
							'wc-block-components-product-price__value',
							'is-discounted',
							priceClassName
						) }
						style={ priceStyle }
					>
						{ value }
					</ins>
				) }
				value={ price }
			/>
		</>
	);
};

export interface ProductPriceProps {
	/**
	 * Where to align the wrapper
	 *
	 * Applies the `wc-block-components-product-price--align-${ align }` utility
	 * class to the wrapper.
	 */
	align?: 'left' | 'center' | 'right' | undefined;
	/**
	 * CSS class for the wrapper
	 */
	className?: string | undefined;
	/**
	 * Currency configuration object
	 */
	currency?: Currency | Record< string, never >;
	/**
	 * The string version of the element to use for the price interpolation
	 *
	 * **Note:** It should contain `<price/>` (which is also the default value)
	 */
	format?: string;
	/**
	 * The current price
	 */
	price?: number | string;
	/**
	 * CSS class for the current price wrapper
	 */
	priceClassName?: string;
	/**
	 * Custom style for the current price
	 */
	priceStyle?: React.CSSProperties | undefined;
	/**
	 * The maximum price in a range
	 *
	 * If both `maxPrice` and `minPrice` are set, the component will be rendered
	 * as a `PriceRange` component, otherwise, this value will be ignored.
	 */
	maxPrice?: number | string | undefined;
	/**
	 * The minimum price in a range
	 *
	 * If both `maxPrice` and `minPrice` are set, the component will be rendered
	 * as a `PriceRange` component, otherwise, this value will be ignored.
	 */
	minPrice?: number | string | undefined;
	/**
	 * The regular price if the item is currently on sale
	 *
	 * If this property exists and is different from the current price, then the
	 * component will be rendered as a `SalePrice` component.
	 */
	regularPrice?: number | string | undefined;
	/**
	 * CSS class to apply to the regular price wrapper
	 */
	regularPriceClassName?: string | undefined;
	/**
	 * Custom style to apply to the regular price wrapper.
	 */
	regularPriceStyle?: React.CSSProperties | undefined;
	/**
	 * Custom margin to apply to the price wrapper.
	 */
	spacingStyle?:
		| Pick<
				React.CSSProperties,
				'marginTop' | 'marginRight' | 'marginBottom' | 'marginLeft'
		  >
		| undefined;
}

const ProductPrice = ( {
	align,
	className,
	currency,
	format = '<price/>',
	maxPrice,
	minPrice,
	price,
	priceClassName,
	priceStyle,
	regularPrice,
	regularPriceClassName,
	regularPriceStyle,
	spacingStyle,
}: ProductPriceProps ): JSX.Element => {
	const wrapperClassName = classNames(
		className,
		'price',
		'wc-block-components-product-price',
		{
			[ `wc-block-components-product-price--align-${ align }` ]: align,
		}
	);

	if ( ! format.includes( '<price/>' ) ) {
		format = '<price/>';
		// eslint-disable-next-line no-console
		console.error( 'Price formats need to include the `<price/>` tag.' );
	}

	const isDiscounted = regularPrice && price !== regularPrice;
	let priceComponent = (
		<span
			className={ classNames(
				'wc-block-components-product-price__value',
				priceClassName
			) }
		/>
	);

	if ( isDiscounted ) {
		priceComponent = (
			<SalePrice
				currency={ currency }
				price={ price }
				priceClassName={ priceClassName }
				priceStyle={ priceStyle }
				regularPrice={ regularPrice }
				regularPriceClassName={ regularPriceClassName }
				regularPriceStyle={ regularPriceStyle }
			/>
		);
	} else if ( minPrice !== undefined && maxPrice !== undefined ) {
		priceComponent = (
			<PriceRange
				currency={ currency }
				maxPrice={ maxPrice }
				minPrice={ minPrice }
				priceClassName={ priceClassName }
				priceStyle={ priceStyle }
			/>
		);
	} else if ( price ) {
		priceComponent = (
			<FormattedMonetaryAmount
				className={ classNames(
					'wc-block-components-product-price__value',
					priceClassName
				) }
				currency={ currency }
				value={ price }
				style={ priceStyle }
			/>
		);
	}

	return (
		<span className={ wrapperClassName } style={ spacingStyle }>
			{ createInterpolateElement( format, {
				price: priceComponent,
			} ) }
		</span>
	);
};

export default ProductPrice;
