/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	CartResponseTotals,
	objectHasProp,
	isString,
} from '@woocommerce/types';

export interface CartTotalItem {
	key: string;
	label: string;
	value: number;
	valueWithTax: number;
}

/**
 * Prepares the total items into a shape usable for display as passed on to
 * registered payment methods.
 *
 * @param {Object}  totals        Current cart total items
 * @param {boolean} needsShipping Whether or not shipping is needed.
 */
export const prepareTotalItems = (
	totals: CartResponseTotals,
	needsShipping: boolean
): CartTotalItem[] => {
	const newTotals = [];

	const factory = ( label: string, property: string ): CartTotalItem => {
		const taxProperty = property + '_tax';
		const value =
			objectHasProp( totals, property ) && isString( totals[ property ] )
				? parseInt( totals[ property ] as string, 10 )
				: 0;
		const tax =
			objectHasProp( totals, taxProperty ) &&
			isString( totals[ taxProperty ] )
				? parseInt( totals[ taxProperty ] as string, 10 )
				: 0;
		return {
			key: property,
			label,
			value,
			valueWithTax: value + tax,
		};
	};

	newTotals.push(
		factory(
			__( 'Subtotal:', 'woo-gutenberg-products-block' ),
			'total_items'
		)
	);

	newTotals.push(
		factory( __( 'Fees:', 'woo-gutenberg-products-block' ), 'total_fees' )
	);

	newTotals.push(
		factory(
			__( 'Discount:', 'woo-gutenberg-products-block' ),
			'total_discount'
		)
	);

	newTotals.push( {
		key: 'total_tax',
		label: __( 'Taxes:', 'woo-gutenberg-products-block' ),
		value: parseInt( totals.total_tax, 10 ),
		valueWithTax: parseInt( totals.total_tax, 10 ),
	} );

	if ( needsShipping ) {
		newTotals.push(
			factory(
				__( 'Shipping:', 'woo-gutenberg-products-block' ),
				'total_shipping'
			)
		);
	}

	return newTotals;
};
