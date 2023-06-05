/**
 * Internal dependencies
 */
import type { CurrencyResponse } from './currency';
import type { CartItem } from './cart';
import type { ProductResponseItem } from './product-response';

export interface CartResponseTotalsItem extends CurrencyResponse {
	total_discount: string;
	total_discount_tax: string;
}

export interface CartResponseCouponItem {
	code: string;
	discount_type: string;
	totals: CartResponseTotalsItem;
}

export interface CartResponseCouponItemWithLabel
	extends CartResponseCouponItem {
	label: string;
}

export type CartResponseCoupons = CartResponseCouponItemWithLabel[];

export interface ResponseFirstNameLastName {
	first_name: string;
	last_name: string;
}

export interface ResponseBaseAddress {
	address_1: string;
	address_2: string;
	city: string;
	state: string;
	postcode: string;
	country: string;
}

export interface ShippingRateItem {
	key: string;
	name: string;
	quantity: number;
}

export interface MetaKeyValue {
	key: string;
	value: string;
}

export type ExtensionsData =
	| Record< string, unknown >
	| Record< string, never >;

export interface CartResponseShippingPackageShippingRate
	extends CurrencyResponse {
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

export interface CartResponseShippingRate {
	/* PackageId can be a string, WooCommerce Subscriptions uses strings for example, but WooCommerce core uses numbers */
	package_id: number | string;
	name: string;
	destination: ResponseBaseAddress;
	items: Array< ShippingRateItem >;
	shipping_rates: Array< CartResponseShippingPackageShippingRate >;
}

export interface CartResponseShippingAddress
	extends ResponseBaseAddress,
		ResponseFirstNameLastName {
	company: string;
	phone: string;
}

export interface CartResponseBillingAddress
	extends CartResponseShippingAddress {
	email: string;
}

export interface CartResponseImageItem {
	id: number;
	src: string;
	thumbnail: string;
	srcset: string;
	sizes: string;
	name: string;
	alt: string;
}

export interface CartResponseVariationItem {
	attribute: string;
	value: string;
}

export interface CartResponseItemPrices extends CurrencyResponse {
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

export interface CartResponseItemTotals extends CurrencyResponse {
	line_subtotal: string;
	line_subtotal_tax: string;
	line_total: string;
	line_total_tax: string;
}

export type CartResponseItem = CartItem;
export interface CartResponseTotalsTaxLineItem {
	name: string;
	price: string;
	rate: string;
}

export interface CartResponseFeeItemTotals extends CurrencyResponse {
	total: string;
	total_tax: string;
}

export type CartResponseFeeItem = {
	id: string;
	name: string;
	totals: CartResponseFeeItemTotals;
};

export interface CartResponseTotals extends CurrencyResponse {
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
	tax_lines: Array< CartResponseTotalsTaxLineItem >;
}

export interface CartResponseErrorItem {
	code: string;
	message: string;
}

export interface CartResponseExtensionItem {
	[ key: string ]: unknown;
}

export interface CartResponse {
	coupons: Array< CartResponseCouponItem >;
	shipping_rates: Array< CartResponseShippingRate >;
	shipping_address: CartResponseShippingAddress;
	billing_address: CartResponseBillingAddress;
	items: Array< CartResponseItem >;
	items_count: number;
	items_weight: number;
	cross_sells: Array< ProductResponseItem >;
	needs_payment: boolean;
	needs_shipping: boolean;
	has_calculated_shipping: boolean;
	fees: Array< CartResponseFeeItem >;
	totals: CartResponseTotals;
	errors: Array< CartResponseErrorItem >;
	payment_methods: string[];
	payment_requirements: Array< unknown >;
	extensions: ExtensionsData;
}
