/**
 * External dependencies
 */
import { render, findByText, queryByText } from '@testing-library/react';

/**
 * Internal dependencies
 */
import { previewCart as mockPreviewCart } from '../../../../../previews/cart';
import {
	textContentMatcher,
	textContentMatcherAcrossSiblings,
} from '../../../../../../../tests/utils/find-by-text';
const baseContextHooks = jest.requireMock( '@woocommerce/base-context/hooks' );
const woocommerceSettings = jest.requireMock( '@woocommerce/settings' );
import SummaryBlock from '../frontend';
import SubtotalBlock from '../../checkout-order-summary-subtotal/frontend';
import FeeBlock from '../../checkout-order-summary-fee/frontend';
import TaxesBlock from '../../checkout-order-summary-taxes/frontend';
import DiscountBlock from '../../checkout-order-summary-discount/frontend';
import CouponsBlock from '../../checkout-order-summary-coupon-form/frontend';
import ShippingBlock from '../../checkout-order-summary-shipping/frontend';
import CartItemsBlock from '../../checkout-order-summary-cart-items/frontend';

const Block = ( { showRateAfterTaxName = false } ) => (
	<SummaryBlock>
		<CartItemsBlock />
		<SubtotalBlock />
		<FeeBlock />
		<DiscountBlock />
		<CouponsBlock />
		<ShippingBlock />
		<TaxesBlock showRateAfterTaxName={ showRateAfterTaxName } />
	</SummaryBlock>
);

const defaultUseStoreCartValue = {
	cartItems: mockPreviewCart.items,
	cartTotals: mockPreviewCart.totals,
	cartCoupons: mockPreviewCart.coupons,
	cartFees: mockPreviewCart.fees,
	cartNeedsShipping: mockPreviewCart.needs_shipping,
	shippingRates: mockPreviewCart.shipping_rates,
	shippingAddress: mockPreviewCart.shipping_address,
	billingAddress: mockPreviewCart.billing_address,
	cartHasCalculatedShipping: mockPreviewCart.has_calculated_shipping,
};

jest.mock( '@woocommerce/base-context/hooks', () => ( {
	...jest.requireActual( '@woocommerce/base-context/hooks' ),

	/*
	We need to redefine this here despite the defaultUseStoreCartValue above
	because jest doesn't like to set up mocks with out of scope variables
	*/
	useStoreCart: jest.fn().mockReturnValue( {
		cartItems: mockPreviewCart.items,
		cartTotals: mockPreviewCart.totals,
		cartCoupons: mockPreviewCart.coupons,
		cartFees: mockPreviewCart.fees,
		cartNeedsShipping: mockPreviewCart.needs_shipping,
		shippingRates: mockPreviewCart.shipping_rates,
		shippingAddress: mockPreviewCart.shipping_address,
		billingAddress: mockPreviewCart.billing_address,
		cartHasCalculatedShipping: mockPreviewCart.has_calculated_shipping,
	} ),
	useShippingData: jest.fn().mockReturnValue( {
		needsShipping: true,
		shippingRates: [
			{
				package_id: 0,
				name: 'Shipping method',
				destination: {
					address_1: '',
					address_2: '',
					city: '',
					state: '',
					postcode: '',
					country: '',
				},
				items: [
					{
						key: 'fb0c0a746719a7596f296344b80cb2b6',
						name: 'Hoodie - Blue, Yes',
						quantity: 1,
					},
					{
						key: '1f0e3dad99908345f7439f8ffabdffc4',
						name: 'Beanie',
						quantity: 1,
					},
				],
				shipping_rates: [
					{
						rate_id: 'flat_rate:1',
						name: 'Flat rate',
						description: '',
						delivery_time: '',
						price: '500',
						taxes: '0',
						instance_id: 1,
						method_id: 'flat_rate',
						meta_data: [
							{
								key: 'Items',
								value: 'Hoodie - Blue, Yes &times; 1, Beanie &times; 1',
							},
						],
						selected: false,
						currency_code: 'USD',
						currency_symbol: '$',
						currency_minor_unit: 2,
						currency_decimal_separator: '.',
						currency_thousand_separator: ',',
						currency_prefix: '$',
						currency_suffix: '',
					},
					{
						rate_id: 'local_pickup:2',
						name: 'Local pickup',
						description: '',
						delivery_time: '',
						price: '0',
						taxes: '0',
						instance_id: 2,
						method_id: 'local_pickup',
						meta_data: [
							{
								key: 'Items',
								value: 'Hoodie - Blue, Yes &times; 1, Beanie &times; 1',
							},
						],
						selected: false,
						currency_code: 'USD',
						currency_symbol: '$',
						currency_minor_unit: 2,
						currency_decimal_separator: '.',
						currency_thousand_separator: ',',
						currency_prefix: '$',
						currency_suffix: '',
					},
					{
						rate_id: 'free_shipping:5',
						name: 'Free shipping',
						description: '',
						delivery_time: '',
						price: '0',
						taxes: '0',
						instance_id: 5,
						method_id: 'free_shipping',
						meta_data: [
							{
								key: 'Items',
								value: 'Hoodie - Blue, Yes &times; 1, Beanie &times; 1',
							},
						],
						selected: true,
						currency_code: 'USD',
						currency_symbol: '$',
						currency_minor_unit: 2,
						currency_decimal_separator: '.',
						currency_thousand_separator: ',',
						currency_prefix: '$',
						currency_suffix: '',
					},
				],
			},
		],
	} ),
} ) );

jest.mock( '@woocommerce/base-context', () => ( {
	...jest.requireActual( '@woocommerce/base-context' ),
	useContainerWidthContext: jest.fn().mockReturnValue( {
		hasContainerWidth: true,
		isLarge: true,
	} ),
} ) );

jest.mock( '@woocommerce/settings', () => {
	const originalModule = jest.requireActual( '@woocommerce/settings' );

	return {
		...originalModule,
		getSetting: jest.fn().mockImplementation( ( setting, ...rest ) => {
			if ( setting === 'couponsEnabled' ) {
				return true;
			}
			return originalModule.getSetting( setting, ...rest );
		} ),
	};
} );

const setUseStoreCartReturnValue = ( value = defaultUseStoreCartValue ) => {
	baseContextHooks.useStoreCart.mockReturnValue( value );
};

const setGetSettingImplementation = ( implementation ) => {
	woocommerceSettings.getSetting.mockImplementation( implementation );
};

const setUseShippingDataReturnValue = ( value ) => {
	baseContextHooks.useShippingData.mockReturnValue( value );
};

describe( 'Checkout Order Summary', () => {
	beforeEach( () => {
		setUseStoreCartReturnValue();
	} );

	it( 'Renders the standard preview items in the sidebar', async () => {
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText( container, 'Warm hat for winter' )
		).toBeInTheDocument();
		expect(
			await findByText( container, 'Lightweight baseball cap' )
		).toBeInTheDocument();

		// Checking if variable product is rendered.
		expect(
			await findByText( container, textContentMatcher( 'Color: Yellow' ) )
		).toBeInTheDocument();
		expect(
			await findByText( container, textContentMatcher( 'Size: Small' ) )
		).toBeInTheDocument();
	} );

	it( 'Renders the items subtotal correctly', async () => {
		const { container } = render( <Block showRateAfterTaxName={ true } /> );

		expect(
			await findByText(
				container,
				textContentMatcherAcrossSiblings( 'Subtotal $40.00' )
			)
		).toBeInTheDocument();
	} );

	// The cart_totals value of useStoreCart is what drives this
	it( 'If discounted items are in the cart the discount subtotal is shown correctly', async () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartTotals: {
				...mockPreviewCart.totals,
				total_discount: 1000,
				total_price: 3800,
			},
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText(
				container,
				textContentMatcherAcrossSiblings( 'Discount -$10.00' )
			)
		).toBeInTheDocument();
	} );

	it( 'If coupons are in the cart they are shown correctly', async () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartTotals: {
				...mockPreviewCart.totals,
				total_discount: 1000,
				total_price: 3800,
			},
			cartCoupons: [
				{
					code: '10off',
					discount_type: 'fixed_cart',
					totals: {
						total_discount: '1000',
						total_discount_tax: '0',
						currency_code: 'USD',
						currency_symbol: '$',
						currency_minor_unit: 2,
						currency_decimal_separator: '.',
						currency_thousand_separator: ',',
						currency_prefix: '$',
						currency_suffix: '',
					},
					label: '10off',
				},
			],
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText( container, 'Coupon: 10off' )
		).toBeInTheDocument();
	} );

	it( 'Shows fees if the cart_fees are set', async () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartFees: [
				{
					totals: {
						currency_code: 'USD',
						currency_decimal_separator: '.',
						currency_minor_unit: 2,
						currency_prefix: '$',
						currency_suffix: '',
						currency_symbol: '$',
						currency_thousand_separator: ',',
						total: 1000,
						total_tax: '0',
					},
				},
			],
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText(
				container,
				textContentMatcherAcrossSiblings( 'Fee $10.00' )
			)
		).toBeInTheDocument();
	} );

	it( 'Shows the coupon entry form when coupons are enabled', async () => {
		setUseStoreCartReturnValue();
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText( container, 'Add a coupon' )
		).toBeInTheDocument();
	} );

	it( 'Does not show the coupon entry if coupons are not enabled', () => {
		setUseStoreCartReturnValue();
		setGetSettingImplementation( ( setting, ...rest ) => {
			if ( setting === 'couponsEnabled' ) {
				return false;
			}
			const originalModule = jest.requireActual(
				'@woocommerce/settings'
			);
			return originalModule.getSetting( setting, ...rest );
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			queryByText( container, 'Coupon code' )
		).not.toBeInTheDocument();
	} );

	it( 'Does not show the shipping section if needsShipping is false on the cart', () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartNeedsShipping: false,
		} );

		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect( queryByText( container, 'Shipping' ) ).not.toBeInTheDocument();
	} );

	it( 'Does not show the taxes section if displayCartPricesIncludingTax is true', () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartTotals: {
				...mockPreviewCart.totals,
				total_tax: '1000',
				tax_lines: [ { name: 'Tax', price: '1000', rate: '5%' } ],
			},
		} );
		setGetSettingImplementation( ( setting, ...rest ) => {
			if ( setting === 'displayCartPricesIncludingTax' ) {
				return true;
			}
			if ( setting === 'taxesEnabled' ) {
				return true;
			}
			const originalModule = jest.requireActual(
				'@woocommerce/settings'
			);
			return originalModule.getSetting( setting, ...rest );
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );

		expect(
			queryByText( container, 'Tax $10.00' )
		).not.toBeInTheDocument();
	} );

	it( 'Shows the taxes section if displayCartPricesIncludingTax is false and a tax total is set', async () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartTotals: {
				...mockPreviewCart.totals,
				total_tax: '1000',
				tax_lines: [ { name: 'Tax', price: '1000', rate: '5%' } ],
			},
		} );
		setUseShippingDataReturnValue( { needsShipping: false } );
		setGetSettingImplementation( ( setting, ...rest ) => {
			if ( setting === 'displayCartPricesIncludingTax' ) {
				return false;
			}
			if ( setting === 'taxesEnabled' ) {
				return true;
			}
			const originalModule = jest.requireActual(
				'@woocommerce/settings'
			);
			return originalModule.getSetting( setting, ...rest );
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText(
				container,
				textContentMatcherAcrossSiblings( 'Taxes $10.00' )
			)
		).toBeInTheDocument();
	} );

	it( 'Shows the grand total correctly', async () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartTotals: {
				...mockPreviewCart.totals,
			},
			cartNeedsShipping: false,
		} );
		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText(
				container,
				textContentMatcherAcrossSiblings( 'Total $49.20' )
			)
		).toBeInTheDocument();
	} );

	it( 'Correctly shows the shipping section if the cart requires shipping', async () => {
		setUseStoreCartReturnValue( {
			...defaultUseStoreCartValue,
			cartTotals: {
				...defaultUseStoreCartValue.cartTotals,
				total_shipping: '4000',
			},
			cartNeedsShipping: true,
			shippingRates: [
				{
					package_id: 0,
					name: 'Shipping method',
					destination: {
						address_1: '',
						address_2: '',
						city: '',
						state: '',
						postcode: '',
						country: '',
					},
					items: [
						{
							key: 'fb0c0a746719a7596f296344b80cb2b6',
							name: 'Hoodie - Blue, Yes',
							quantity: 1,
						},
						{
							key: '1f0e3dad99908345f7439f8ffabdffc4',
							name: 'Beanie',
							quantity: 1,
						},
					],
					shipping_rates: [
						{
							rate_id: 'free_shipping:5',
							name: 'Free shipping',
							description: '',
							delivery_time: '',
							price: '4000',
							taxes: '0',
							instance_id: 5,
							method_id: 'free_shipping',
							meta_data: [
								{
									key: 'Items',
									value: 'Hoodie - Blue, Yes &times; 1, Beanie &times; 1',
								},
							],
							selected: true,
							currency_code: 'USD',
							currency_symbol: '$',
							currency_minor_unit: 2,
							currency_decimal_separator: '.',
							currency_thousand_separator: ',',
							currency_prefix: '$',
							currency_suffix: '',
						},
					],
				},
			],
		} );

		const { container } = render( <Block showRateAfterTaxName={ true } /> );
		expect(
			await findByText(
				container,
				textContentMatcherAcrossSiblings(
					'Shipping $40.00 Free shipping'
				)
			)
		).toBeInTheDocument();
	} );
} );
