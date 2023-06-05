/**
 * External dependencies
 */
import classnames from 'classnames';
import { __, sprintf } from '@wordpress/i18n';
import { speak } from '@wordpress/a11y';
import QuantitySelector from '@woocommerce/base-components/quantity-selector';
import ProductPrice from '@woocommerce/base-components/product-price';
import ProductName from '@woocommerce/base-components/product-name';
import {
	useStoreCartItemQuantity,
	useStoreEvents,
	useStoreCart,
} from '@woocommerce/base-context/hooks';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import { applyCheckoutFilter, mustContain } from '@woocommerce/blocks-checkout';
import Dinero from 'dinero.js';
import { forwardRef, useMemo } from '@wordpress/element';
import type { CartItem } from '@woocommerce/types';
import { objectHasProp, Currency } from '@woocommerce/types';
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import ProductBackorderBadge from '../product-backorder-badge';
import ProductImage from '../product-image';
import ProductLowStockBadge from '../product-low-stock-badge';
import ProductMetadata from '../product-metadata';
import ProductSaleBadge from '../product-sale-badge';

/**
 * Convert a Dinero object with precision to store currency minor unit.
 *
 * @param {Dinero} priceObject Price object to convert.
 * @param {Object} currency    Currency data.
 * @return {number} Amount with new minor unit precision.
 */
const getAmountFromRawPrice = (
	priceObject: Dinero.Dinero,
	currency: Currency
) => {
	return priceObject.convertPrecision( currency.minorUnit ).getAmount();
};

const productPriceValidation = ( value: string ) =>
	mustContain( value, '<price/>' );

interface CartLineItemRowProps {
	lineItem: CartItem | Record< string, never >;
	onRemove?: () => void;
	tabIndex?: number;
}

/**
 * Cart line item table row component.
 */
const CartLineItemRow: React.ForwardRefExoticComponent<
	CartLineItemRowProps & React.RefAttributes< HTMLTableRowElement >
> = forwardRef< HTMLTableRowElement, CartLineItemRowProps >(
	(
		{ lineItem, onRemove = () => void null, tabIndex },
		ref
	): JSX.Element => {
		const {
			name: initialName = '',
			catalog_visibility: catalogVisibility = 'visible',
			short_description: shortDescription = '',
			description: fullDescription = '',
			low_stock_remaining: lowStockRemaining = null,
			show_backorder_badge: showBackorderBadge = false,
			quantity_limits: quantityLimits = {
				minimum: 1,
				maximum: 99,
				multiple_of: 1,
				editable: true,
			},
			sold_individually: soldIndividually = false,
			permalink = '',
			images = [],
			variation = [],
			item_data: itemData = [],
			prices = {
				currency_code: 'USD',
				currency_minor_unit: 2,
				currency_symbol: '$',
				currency_prefix: '$',
				currency_suffix: '',
				currency_decimal_separator: '.',
				currency_thousand_separator: ',',
				price: '0',
				regular_price: '0',
				sale_price: '0',
				price_range: null,
				raw_prices: {
					precision: 6,
					price: '0',
					regular_price: '0',
					sale_price: '0',
				},
			},
			totals = {
				currency_code: 'USD',
				currency_minor_unit: 2,
				currency_symbol: '$',
				currency_prefix: '$',
				currency_suffix: '',
				currency_decimal_separator: '.',
				currency_thousand_separator: ',',
				line_subtotal: '0',
				line_subtotal_tax: '0',
			},
			extensions,
		} = lineItem;

		const { quantity, setItemQuantity, removeItem, isPendingDelete } =
			useStoreCartItemQuantity( lineItem );
		const { dispatchStoreEvent } = useStoreEvents();

		// Prepare props to pass to the applyCheckoutFilter filter.
		// We need to pluck out receiveCart.
		// eslint-disable-next-line no-unused-vars
		const { receiveCart, ...cart } = useStoreCart();
		const arg = useMemo(
			() => ( {
				context: 'cart',
				cartItem: lineItem,
				cart,
			} ),
			[ lineItem, cart ]
		);
		const priceCurrency = getCurrencyFromPriceResponse( prices );
		const name = applyCheckoutFilter( {
			filterName: 'itemName',
			defaultValue: initialName,
			extensions,
			arg,
		} );

		const regularAmountSingle = Dinero( {
			amount: parseInt( prices.raw_prices.regular_price, 10 ),
			precision: prices.raw_prices.precision,
		} );
		const purchaseAmountSingle = Dinero( {
			amount: parseInt( prices.raw_prices.price, 10 ),
			precision: prices.raw_prices.precision,
		} );
		const saleAmountSingle =
			regularAmountSingle.subtract( purchaseAmountSingle );
		const saleAmount = saleAmountSingle.multiply( quantity );
		const totalsCurrency = getCurrencyFromPriceResponse( totals );
		let lineSubtotal = parseInt( totals.line_subtotal, 10 );
		if ( getSetting( 'displayCartPricesIncludingTax', false ) ) {
			lineSubtotal += parseInt( totals.line_subtotal_tax, 10 );
		}
		const subtotalPrice = Dinero( {
			amount: lineSubtotal,
			precision: totalsCurrency.minorUnit,
		} );

		const firstImage = images.length ? images[ 0 ] : {};
		const isProductHiddenFromCatalog =
			catalogVisibility === 'hidden' || catalogVisibility === 'search';

		const cartItemClassNameFilter = applyCheckoutFilter( {
			filterName: 'cartItemClass',
			defaultValue: '',
			extensions,
			arg,
		} );

		// Allow extensions to filter how the price is displayed. Ie: prepending or appending some values.
		const productPriceFormat = applyCheckoutFilter( {
			filterName: 'cartItemPrice',
			defaultValue: '<price/>',
			extensions,
			arg,
			validation: productPriceValidation,
		} );

		const subtotalPriceFormat = applyCheckoutFilter( {
			filterName: 'subtotalPriceFormat',
			defaultValue: '<price/>',
			extensions,
			arg,
			validation: productPriceValidation,
		} );

		const saleBadgePriceFormat = applyCheckoutFilter( {
			filterName: 'saleBadgePriceFormat',
			defaultValue: '<price/>',
			extensions,
			arg,
			validation: productPriceValidation,
		} );

		const showRemoveItemLink = applyCheckoutFilter( {
			filterName: 'showRemoveItemLink',
			defaultValue: true,
			extensions,
			arg,
		} );

		return (
			<tr
				className={ classnames(
					'wc-block-cart-items__row',
					cartItemClassNameFilter,
					{
						'is-disabled': isPendingDelete,
					}
				) }
				ref={ ref }
				tabIndex={ tabIndex }
			>
				{ /* If the image has no alt text, this link is unnecessary and can be hidden. */ }
				<td
					className="wc-block-cart-item__image"
					aria-hidden={
						! objectHasProp( firstImage, 'alt' ) || ! firstImage.alt
					}
				>
					{ /* We don't need to make it focusable, because product name has the same link. */ }
					{ isProductHiddenFromCatalog ? (
						<ProductImage
							image={ firstImage }
							fallbackAlt={ name }
						/>
					) : (
						<a href={ permalink } tabIndex={ -1 }>
							<ProductImage
								image={ firstImage }
								fallbackAlt={ name }
							/>
						</a>
					) }
				</td>
				<td className="wc-block-cart-item__product">
					<div className="wc-block-cart-item__wrap">
						<ProductName
							disabled={
								isPendingDelete || isProductHiddenFromCatalog
							}
							name={ name }
							permalink={ permalink }
						/>
						{ showBackorderBadge ? (
							<ProductBackorderBadge />
						) : (
							!! lowStockRemaining && (
								<ProductLowStockBadge
									lowStockRemaining={ lowStockRemaining }
								/>
							)
						) }

						<div className="wc-block-cart-item__prices">
							<ProductPrice
								currency={ priceCurrency }
								regularPrice={ getAmountFromRawPrice(
									regularAmountSingle,
									priceCurrency
								) }
								price={ getAmountFromRawPrice(
									purchaseAmountSingle,
									priceCurrency
								) }
								format={ subtotalPriceFormat }
							/>
						</div>

						<ProductSaleBadge
							currency={ priceCurrency }
							saleAmount={ getAmountFromRawPrice(
								saleAmountSingle,
								priceCurrency
							) }
							format={ saleBadgePriceFormat }
						/>

						<ProductMetadata
							shortDescription={ shortDescription }
							fullDescription={ fullDescription }
							itemData={ itemData }
							variation={ variation }
						/>

						<div className="wc-block-cart-item__quantity">
							{ ! soldIndividually &&
								!! quantityLimits.editable && (
									<QuantitySelector
										disabled={ isPendingDelete }
										quantity={ quantity }
										minimum={ quantityLimits.minimum }
										maximum={ quantityLimits.maximum }
										step={ quantityLimits.multiple_of }
										onChange={ ( newQuantity ) => {
											setItemQuantity( newQuantity );
											dispatchStoreEvent(
												'cart-set-item-quantity',
												{
													product: lineItem,
													quantity: newQuantity,
												}
											);
										} }
										itemName={ name }
									/>
								) }
							{ showRemoveItemLink && (
								<button
									className="wc-block-cart-item__remove-link"
									aria-label={ sprintf(
										/* translators: %s refers to the item's name in the cart. */
										__(
											'Remove %s from cart',
											'woo-gutenberg-products-block'
										),
										name
									) }
									onClick={ () => {
										onRemove();
										removeItem();
										dispatchStoreEvent(
											'cart-remove-item',
											{
												product: lineItem,
												quantity,
											}
										);
										speak(
											sprintf(
												/* translators: %s refers to the item name in the cart. */
												__(
													'%s has been removed from your cart.',
													'woo-gutenberg-products-block'
												),
												name
											)
										);
									} }
									disabled={ isPendingDelete }
								>
									{ __(
										'Remove item',
										'woo-gutenberg-products-block'
									) }
								</button>
							) }
						</div>
					</div>
				</td>
				<td className="wc-block-cart-item__total">
					<div className="wc-block-cart-item__total-price-and-sale-badge-wrapper">
						<ProductPrice
							currency={ totalsCurrency }
							format={ productPriceFormat }
							price={ subtotalPrice.getAmount() }
						/>

						{ quantity > 1 && (
							<ProductSaleBadge
								currency={ priceCurrency }
								saleAmount={ getAmountFromRawPrice(
									saleAmount,
									priceCurrency
								) }
								format={ saleBadgePriceFormat }
							/>
						) }
					</div>
				</td>
			</tr>
		);
	}
);
export default CartLineItemRow;
