/**
 * Internal dependencies
 */
import type { CartResponseTotals } from '../type-defs';
import { isObject } from './object';

// It is the only way to create a type that contains all the object's keys and gets type-checking.
// This is useful because we want to check that the keys object ALWAYS contains all the object's keys.
// https://stackoverflow.com/questions/52028791/make-a-generic-type-arraykeyof-t-require-all-keys-of-t

type CartResponseTotalsKeys = Record< keyof CartResponseTotals, 0 >;

export const isCartResponseTotals = (
	value: unknown
): value is CartResponseTotals => {
	if ( ! isObject( value ) ) {
		return false;
	}

	const keys: CartResponseTotalsKeys = {
		total_items: 0,
		total_items_tax: 0,
		total_fees: 0,
		total_fees_tax: 0,
		total_discount: 0,
		total_discount_tax: 0,
		total_shipping: 0,
		total_shipping_tax: 0,
		total_price: 0,
		total_tax: 0,
		tax_lines: 0,
		currency_code: 0,
		currency_symbol: 0,
		currency_minor_unit: 0,
		currency_decimal_separator: 0,
		currency_thousand_separator: 0,
		currency_prefix: 0,
		currency_suffix: 0,
	};

	return Object.keys( keys ).every( ( key ) => key in value );
};
