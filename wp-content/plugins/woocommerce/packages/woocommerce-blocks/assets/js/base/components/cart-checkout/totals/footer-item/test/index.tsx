/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import { allSettings } from '@woocommerce/settings';
import { Currency, CartResponseTotals } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import TotalsFooterItem from '../index';

describe( 'TotalsFooterItem', () => {
	beforeEach( () => {
		allSettings.taxesEnabled = true;
		allSettings.displayCartPricesIncludingTax = true;
	} );
	const currency: Currency = {
		code: 'GBP',
		decimalSeparator: '.',
		minorUnit: 2,
		prefix: '£',
		suffix: '',
		symbol: '£',
		thousandSeparator: ',',
	};

	const values: CartResponseTotals = {
		currency_code: 'GBP',
		currency_decimal_separator: '.',
		currency_minor_unit: 2,
		currency_prefix: '£',
		currency_suffix: '',
		currency_symbol: '£',
		currency_thousand_separator: ',',
		tax_lines: [],
		total_discount: '0',
		total_discount_tax: '0',
		total_fees: '0',
		total_fees_tax: '0',
		total_items: '7100',
		total_items_tax: '0',
		total_price: '8500',
		total_shipping: '0',
		total_shipping_tax: '0',
		total_tax: '0',
	};

	it( 'Does not show the "including %s of tax" line if tax is 0', () => {
		const { container } = render(
			<TotalsFooterItem currency={ currency } values={ values } />
		);
		expect( container ).toMatchSnapshot();
	} );

	it( 'Does not show the "including %s of tax" line if tax is disabled', () => {
		allSettings.taxesEnabled = false;
		/* This shouldn't ever happen if taxes are disabled, but this is to test whether the taxesEnabled setting works */
		const valuesWithTax = {
			...values,
			total_tax: '100',
			total_items_tax: '100',
		};
		const { container } = render(
			<TotalsFooterItem currency={ currency } values={ valuesWithTax } />
		);
		expect( container ).toMatchSnapshot();
	} );

	it( 'Shows the "including %s of tax" line if tax is greater than 0', () => {
		const valuesWithTax = {
			...values,
			total_tax: '100',
			total_items_tax: '100',
		};
		const { container } = render(
			<TotalsFooterItem currency={ currency } values={ valuesWithTax } />
		);
		expect( container ).toMatchSnapshot();
	} );

	it( 'Shows the "including %s TAX LABEL" line with single tax label', () => {
		const valuesWithTax = {
			...values,
			total_tax: '100',
			total_items_tax: '100',
			tax_lines: [ { name: '10% VAT', price: '100', rate: '10.000' } ],
		};
		const { container } = render(
			<TotalsFooterItem currency={ currency } values={ valuesWithTax } />
		);
		expect( container ).toMatchSnapshot();
	} );

	it( 'Shows the "including %s TAX LABELS" line with multiple tax labels', () => {
		const valuesWithTax = {
			...values,
			total_tax: '100',
			total_items_tax: '100',
			tax_lines: [
				{ name: '10% VAT', price: '50', rate: '10.000' },
				{ name: '5% VAT', price: '50', rate: '5.000' },
			],
		};
		const { container } = render(
			<TotalsFooterItem currency={ currency } values={ valuesWithTax } />
		);
		expect( container ).toMatchSnapshot();
	} );
} );
