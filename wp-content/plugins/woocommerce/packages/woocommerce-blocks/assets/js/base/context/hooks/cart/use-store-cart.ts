/** @typedef { import('@woocommerce/type-defs/hooks').StoreCart } StoreCart */

/**
 * External dependencies
 */
import fastDeepEqual from 'fast-deep-equal/es6';
import { useRef } from '@wordpress/element';
import {
	CART_STORE_KEY as storeKey,
	EMPTY_CART_COUPONS,
	EMPTY_CART_ITEMS,
	EMPTY_CART_CROSS_SELLS,
	EMPTY_CART_FEES,
	EMPTY_CART_ITEM_ERRORS,
	EMPTY_CART_ERRORS,
	EMPTY_SHIPPING_RATES,
	EMPTY_TAX_LINES,
	EMPTY_PAYMENT_METHODS,
	EMPTY_PAYMENT_REQUIREMENTS,
	EMPTY_EXTENSIONS,
} from '@woocommerce/block-data';
import { useSelect } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import type {
	StoreCart,
	CartResponseTotals,
	CartResponseFeeItem,
	CartResponseBillingAddress,
	CartResponseShippingAddress,
	CartResponseCouponItem,
	CartResponseCoupons,
} from '@woocommerce/types';
import { emptyHiddenAddressFields } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import { useEditorContext } from '../../providers/editor-context';
import { useStoreCartEventListeners } from './use-store-cart-event-listeners';

declare module '@wordpress/html-entities' {
	// eslint-disable-next-line @typescript-eslint/no-shadow
	export function decodeEntities< T >( coupon: T ): T;
}
const defaultShippingAddress: CartResponseShippingAddress = {
	first_name: '',
	last_name: '',
	company: '',
	address_1: '',
	address_2: '',
	city: '',
	state: '',
	postcode: '',
	country: '',
	phone: '',
};

const defaultBillingAddress: CartResponseBillingAddress = {
	...defaultShippingAddress,
	email: '',
};

const defaultCartTotals: CartResponseTotals = {
	total_items: '',
	total_items_tax: '',
	total_fees: '',
	total_fees_tax: '',
	total_discount: '',
	total_discount_tax: '',
	total_shipping: '',
	total_shipping_tax: '',
	total_price: '',
	total_tax: '',
	tax_lines: EMPTY_TAX_LINES,
	currency_code: '',
	currency_symbol: '',
	currency_minor_unit: 2,
	currency_decimal_separator: '',
	currency_thousand_separator: '',
	currency_prefix: '',
	currency_suffix: '',
};

const decodeValues = (
	object: Record< string, unknown >
): Record< string, unknown > =>
	Object.fromEntries(
		Object.entries( object ).map( ( [ key, value ] ) => [
			key,
			decodeEntities( value ),
		] )
	);

/**
 * @constant
 * @type  {StoreCart} Object containing cart data.
 */
export const defaultCartData: StoreCart = {
	cartCoupons: EMPTY_CART_COUPONS,
	cartItems: EMPTY_CART_ITEMS,
	cartFees: EMPTY_CART_FEES,
	cartItemsCount: 0,
	cartItemsWeight: 0,
	crossSellsProducts: EMPTY_CART_CROSS_SELLS,
	cartNeedsPayment: true,
	cartNeedsShipping: true,
	cartItemErrors: EMPTY_CART_ITEM_ERRORS,
	cartTotals: defaultCartTotals,
	cartIsLoading: true,
	cartErrors: EMPTY_CART_ERRORS,
	billingAddress: defaultBillingAddress,
	shippingAddress: defaultShippingAddress,
	shippingRates: EMPTY_SHIPPING_RATES,
	isLoadingRates: false,
	cartHasCalculatedShipping: false,
	paymentMethods: EMPTY_PAYMENT_METHODS,
	paymentRequirements: EMPTY_PAYMENT_REQUIREMENTS,
	receiveCart: () => undefined,
	receiveCartContents: () => undefined,
	extensions: EMPTY_EXTENSIONS,
};

/**
 * This is a custom hook that is wired up to the `wc/store/cart` data
 * store.
 *
 * @param {Object}  options              An object declaring the various
 *                                       collection arguments.
 * @param {boolean} options.shouldSelect If false, the previous results will be
 *                                       returned and internal selects will not
 *                                       fire.
 *
 * @return {StoreCart} Object containing cart data.
 */

export const useStoreCart = (
	options: { shouldSelect: boolean } = { shouldSelect: true }
): StoreCart => {
	const { isEditor, previewData } = useEditorContext();
	const previewCart = previewData?.previewCart;
	const { shouldSelect } = options;
	const currentResults = useRef();

	// This will keep track of jQuery and DOM events that invalidate the store resolution.
	useStoreCartEventListeners();

	const results: StoreCart = useSelect(
		( select, { dispatch } ) => {
			if ( ! shouldSelect ) {
				return defaultCartData;
			}

			if ( isEditor ) {
				return {
					cartCoupons: previewCart.coupons,
					cartItems: previewCart.items,
					crossSellsProducts: previewCart.cross_sells,
					cartFees: previewCart.fees,
					cartItemsCount: previewCart.items_count,
					cartItemsWeight: previewCart.items_weight,
					cartNeedsPayment: previewCart.needs_payment,
					cartNeedsShipping: previewCart.needs_shipping,
					cartItemErrors: EMPTY_CART_ITEM_ERRORS,
					cartTotals: previewCart.totals,
					cartIsLoading: false,
					cartErrors: EMPTY_CART_ERRORS,
					billingData: defaultBillingAddress,
					billingAddress: defaultBillingAddress,
					shippingAddress: defaultShippingAddress,
					extensions: EMPTY_EXTENSIONS,
					shippingRates: previewCart.shipping_rates,
					isLoadingRates: false,
					cartHasCalculatedShipping:
						previewCart.has_calculated_shipping,
					paymentRequirements: previewCart.paymentRequirements,
					receiveCart:
						typeof previewCart?.receiveCart === 'function'
							? previewCart.receiveCart
							: () => undefined,
					receiveCartContents:
						typeof previewCart?.receiveCartContents === 'function'
							? previewCart.receiveCartContents
							: () => undefined,
				};
			}

			const store = select( storeKey );
			const cartData = store.getCartData();
			const cartErrors = store.getCartErrors();
			const cartTotals = store.getCartTotals();
			const cartIsLoading =
				! store.hasFinishedResolution( 'getCartData' );

			const isLoadingRates = store.isCustomerDataUpdating();
			const { receiveCart, receiveCartContents } = dispatch( storeKey );
			const billingAddress = decodeValues( cartData.billingAddress );
			const shippingAddress = cartData.needsShipping
				? decodeValues( cartData.shippingAddress )
				: billingAddress;
			const cartFees =
				cartData.fees.length > 0
					? cartData.fees.map( ( fee: CartResponseFeeItem ) =>
							decodeValues( fee )
					  )
					: EMPTY_CART_FEES;

			// Add a text property to the coupon to allow extensions to modify
			// the text used to display the coupon, without affecting the
			// functionality when it comes to removing the coupon.
			const cartCoupons: CartResponseCoupons =
				cartData.coupons.length > 0
					? cartData.coupons.map(
							( coupon: CartResponseCouponItem ) => ( {
								...coupon,
								label: coupon.code,
							} )
					  )
					: EMPTY_CART_COUPONS;

			return {
				cartCoupons,
				cartItems: cartData.items,
				crossSellsProducts: cartData.crossSells,
				cartFees,
				cartItemsCount: cartData.itemsCount,
				cartItemsWeight: cartData.itemsWeight,
				cartNeedsPayment: cartData.needsPayment,
				cartNeedsShipping: cartData.needsShipping,
				cartItemErrors: cartData.errors,
				cartTotals,
				cartIsLoading,
				cartErrors,
				billingData: emptyHiddenAddressFields( billingAddress ),
				billingAddress: emptyHiddenAddressFields( billingAddress ),
				shippingAddress: emptyHiddenAddressFields( shippingAddress ),
				extensions: cartData.extensions,
				shippingRates: cartData.shippingRates,
				isLoadingRates,
				cartHasCalculatedShipping: cartData.hasCalculatedShipping,
				paymentRequirements: cartData.paymentRequirements,
				receiveCart,
				receiveCartContents,
			};
		},
		[ shouldSelect ]
	);

	if (
		! currentResults.current ||
		! fastDeepEqual( currentResults.current, results )
	) {
		currentResults.current = results;
	}

	return currentResults.current;
};
