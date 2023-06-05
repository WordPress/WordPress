/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import ProductPrice from '../index';

describe( 'ProductPrice', () => {
	const currency = {
		code: 'GBP',
		currency_code: 'GBP',
		currency_decimal_separator: '.',
		currency_minor_unit: 2,
		currency_prefix: '£',
		currency_suffix: '',
		currency_symbol: '£',
		currency_thousand_separator: ',',
		decimalSeparator: '.',
		minorUnit: 2,
		prefix: '£',
		price: '61400',
		price_range: null,
		raw_prices: {
			precision: 6,
			price: '614000000',
			regular_price: '614000000',
			sale_price: '614000000',
		},
		regular_price: '61400',
		sale_price: '61400',
		suffix: '',
		symbol: '£',
		thousandSeparator: ',',
	};

	test( 'should use default price if no format is provided', () => {
		const component = TestRenderer.create(
			<ProductPrice
				price={ 50 }
				regularPrice={ 100 }
				currency={ currency }
			/>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should apply the format if one is provided', () => {
		const component = TestRenderer.create(
			<ProductPrice
				price={ 50 }
				regularPrice={ 100 }
				currency={ currency }
				format="pre price <price/> Test format"
			/>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
