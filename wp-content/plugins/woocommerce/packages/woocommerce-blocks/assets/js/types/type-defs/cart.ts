/**
 * External dependencies
 */
import type { CurrencyCode } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import {
	MetaKeyValue,
	ShippingRateItem,
	ExtensionsData,
} from './cart-response';

import {
	ProductResponseItemData,
	ProductResponseItem,
} from './product-response';

export interface CurrencyInfo {
	currency_code: CurrencyCode;
	currency_symbol: string;
	currency_minor_unit: number;
	currency_decimal_separator: string;
	currency_thousand_separator: string;
	currency_prefix: string;
	currency_suffix: string;
}

export interface CartTotalsItem extends CurrencyInfo {
	total_discount: string;
	total_discount_tax: string;
}

export interface CartCouponItem {
	code: string;
	label: string;
	discount_type: string;
	totals: CartTotalsItem;
}

export interface FirstNameLastName {
	first_name: string;
	last_name: string;
}

export interface BaseAddress {
	address_1: string;
	address_2: string;
	city: string;
	state: string;
	postcode: string;
	country: string;
}

export interface CartShippingPackageShippingRate extends CurrencyInfo {
	rate_id: string;
	name: string;
	description: string;
	delivery_time: string;
	price: string;
	taxes: string;
	instance_id: number;
	method_id: string;
	meta_data: Array< MetaKeyValue >;
	selected: boolean;
}

export interface CartShippingRate {
	package_id: string | number;
	name: string;
	destination: BaseAddress;
	items: Array< ShippingRateItem >;
	shipping_rates: Array< CartShippingPackageShippingRate >;
}

export interface CartShippingAddress extends BaseAddress, FirstNameLastName {
	company: string;
	phone: string;
}

export interface CartBillingAddress extends CartShippingAddress {
	email: string;
}

export interface CartImageItem {
	id: number;
	src: string;
	thumbnail: string;
	srcset: string;
	sizes: string;
	name: string;
	alt: string;
}

export interface CartVariationItem {
	attribute: string;
	value: string;
}

export interface CartItemPrices extends CurrencyInfo {
	price: string;
	regular_price: string;
	sale_price: string;
	price_range: null | { min_amount: string; max_amount: string };
	raw_prices: {
		precision: number;
		price: string;
		regular_price: string;
		sale_price: string;
	};
}

export interface CartItemTotals extends CurrencyInfo {
	line_subtotal: string;
	line_subtotal_tax: string;
	line_total: string;
	line_total_tax: string;
}

export type CatalogVisibility = 'catalog' | 'hidden' | 'search' | 'visible';

export interface CartItem {
	key: string;
	id: number;
	quantity: number;
	catalog_visibility: CatalogVisibility;
	quantity_limits: {
		minimum: number;
		maximum: number;
		multiple_of: number;
		editable: boolean;
	};
	name: string;
	summary: string;
	short_description: string;
	description: string;
	sku: string;
	low_stock_remaining: null | number;
	backorders_allowed: boolean;
	show_backorder_badge: boolean;
	sold_individually: boolean;
	permalink: string;
	images: Array< CartImageItem >;
	variation: Array< CartVariationItem >;
	prices: CartItemPrices;
	totals: CartItemTotals;
	extensions: ExtensionsData;
	item_data: ProductResponseItemData[];
}

export interface CartTotalsTaxLineItem {
	name: string;
	price: string;
	rate: string;
}

export interface CartFeeItemTotals extends CurrencyInfo {
	total: string;
	total_tax: string;
}

export interface CartFeeItem {
	id: string;
	name: string;
	totals: CartFeeItemTotals;
}

export interface CartTotals extends CurrencyInfo {
	total_items: string;
	total_items_tax: string;
	total_fees: string;
	total_fees_tax: string;
	total_discount: string;
	total_discount_tax: string;
	total_shipping: string;
	total_shipping_tax: string;
	total_price: string;
	total_tax: string;
	tax_lines: Array< CartTotalsTaxLineItem >;
}

export interface CartErrorItem {
	code: string;
	message: string;
}

export interface Cart extends Record< string, unknown > {
	coupons: Array< CartCouponItem >;
	shippingRates: Array< CartShippingRate >;
	shippingAddress: CartShippingAddress;
	billingAddress: CartBillingAddress;
	items: Array< CartItem >;
	itemsCount: number;
	itemsWeight: number;
	crossSells: Array< ProductResponseItem >;
	needsPayment: boolean;
	needsShipping: boolean;
	hasCalculatedShipping: boolean;
	fees: Array< CartFeeItem >;
	totals: CartTotals;
	errors: Array< CartErrorItem >;
	paymentMethods: Array< string >;
	paymentRequirements: Array< string >;
	extensions: ExtensionsData;
}
export interface CartMeta {
	updatingCustomerData: boolean;
	updatingSelectedRate: boolean;
	isCartDataStale: boolean;
	applyingCoupon: string;
	removingCoupon: string;
	/* Whether the full address has been previously pushed to the server */
	fullShippingAddressPushed: boolean;
}
export interface ExtensionCartUpdateArgs {
	data: Record< string, unknown >;
	namespace: string;
}

export interface BillingAddressShippingAddress {
	billing_address: Partial< CartBillingAddress >;
	shipping_address: Partial< CartShippingAddress >;
}
