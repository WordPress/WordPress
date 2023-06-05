/**
 * External dependencies
 */
import { screen, render } from '@testing-library/react';
import { SlotFillProvider } from '@woocommerce/blocks-checkout';
import { previewCart as mockPreviewCart } from '@woocommerce/resource-previews';
import * as wpData from '@wordpress/data';
import * as baseContextHooks from '@woocommerce/base-context/hooks';

/**
 * Internal dependencies
 */
import { TotalsShipping } from '../index';

jest.mock( '@wordpress/data', () => ( {
	__esModule: true,
	...jest.requireActual( '@wordpress/data' ),
	useSelect: jest.fn(),
} ) );

// Mock use select so we can override it when wc/store/checkout is accessed, but return the original select function if any other store is accessed.
wpData.useSelect.mockImplementation(
	jest.fn().mockImplementation( ( passedMapSelect ) => {
		const mockedSelect = jest.fn().mockImplementation( ( storeName ) => {
			if ( storeName === 'wc/store/checkout' ) {
				return {
					prefersCollection() {
						return false;
					},
				};
			}
			return jest.requireActual( '@wordpress/data' ).select( storeName );
		} );
		passedMapSelect( mockedSelect, {
			dispatch: jest.requireActual( '@wordpress/data' ).dispatch,
		} );
	} )
);

const shippingAddress = {
	first_name: 'John',
	last_name: 'Doe',
	company: 'Company',
	address_1: '409 Main Street',
	address_2: 'Apt 1',
	city: 'London',
	postcode: 'W1T 4JG',
	country: 'GB',
	state: '',
	email: 'john.doe@company',
	phone: '+1234567890',
};

jest.mock( '@woocommerce/base-context/hooks', () => {
	return {
		__esModule: true,
		...jest.requireActual( '@woocommerce/base-context/hooks' ),
		useShippingData: jest.fn(),
		useStoreCart: jest.fn(),
	};
} );
baseContextHooks.useShippingData.mockReturnValue( {
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
} );
baseContextHooks.useStoreCart.mockReturnValue( {
	cartItems: mockPreviewCart.items,
	cartTotals: [ mockPreviewCart.totals ],
	cartCoupons: mockPreviewCart.coupons,
	cartFees: mockPreviewCart.fees,
	cartNeedsShipping: mockPreviewCart.needs_shipping,
	shippingRates: [],
	shippingAddress,
	billingAddress: mockPreviewCart.billing_address,
	cartHasCalculatedShipping: mockPreviewCart.has_calculated_shipping,
	isLoadingRates: false,
} );

describe( 'TotalsShipping', () => {
	it( 'should show correct calculator button label if address is complete', () => {
		render(
			<SlotFillProvider>
				<TotalsShipping
					currency={ {
						code: 'USD',
						symbol: '$',
						position: 'left',
						precision: 2,
					} }
					values={ {
						total_shipping: '0',
						total_shipping_tax: '0',
					} }
					showCalculator={ true }
					showRateSelector={ true }
					isCheckout={ true }
					className={ '' }
				/>
			</SlotFillProvider>
		);
		expect(
			screen.getByText(
				'Shipping to W1T 4JG, London, United Kingdom (UK)'
			)
		).toBeInTheDocument();
		expect( screen.getByText( 'Change address' ) ).toBeInTheDocument();
	} );
	it( 'should show correct calculator button label if address is incomplete', () => {
		baseContextHooks.useStoreCart.mockReturnValue( {
			cartItems: mockPreviewCart.items,
			cartTotals: [ mockPreviewCart.totals ],
			cartCoupons: mockPreviewCart.coupons,
			cartFees: mockPreviewCart.fees,
			cartNeedsShipping: mockPreviewCart.needs_shipping,
			shippingRates: [],
			shippingAddress: {
				...shippingAddress,
				city: '',
				country: '',
				postcode: '',
			},
			billingAddress: mockPreviewCart.billing_address,
			cartHasCalculatedShipping: mockPreviewCart.has_calculated_shipping,
			isLoadingRates: false,
		} );
		render(
			<SlotFillProvider>
				<TotalsShipping
					currency={ {
						code: 'USD',
						symbol: '$',
						position: 'left',
						precision: 2,
					} }
					values={ {
						total_shipping: '0',
						total_shipping_tax: '0',
					} }
					showCalculator={ true }
					showRateSelector={ true }
					isCheckout={ true }
					className={ '' }
				/>
			</SlotFillProvider>
		);
		expect(
			screen.queryByText( 'Change address' )
		).not.toBeInTheDocument();
		expect(
			screen.getByText( 'Add an address for shipping options' )
		).toBeInTheDocument();
	} );
	it( 'does not show the calculator button when default rates are available and no address has been entered', () => {
		baseContextHooks.useStoreCart.mockReturnValue( {
			cartItems: mockPreviewCart.items,
			cartTotals: [ mockPreviewCart.totals ],
			cartCoupons: mockPreviewCart.coupons,
			cartFees: mockPreviewCart.fees,
			cartNeedsShipping: mockPreviewCart.needs_shipping,
			shippingRates: mockPreviewCart.shipping_rates,
			shippingAddress: {
				...shippingAddress,
				city: '',
				country: '',
				postcode: '',
			},
			billingAddress: mockPreviewCart.billing_address,
			cartHasCalculatedShipping: mockPreviewCart.has_calculated_shipping,
			isLoadingRates: false,
		} );
		render(
			<SlotFillProvider>
				<TotalsShipping
					currency={ {
						code: 'USD',
						symbol: '$',
						position: 'left',
						precision: 2,
					} }
					values={ {
						total_shipping: '0',
						total_shipping_tax: '0',
					} }
					showCalculator={ true }
					showRateSelector={ true }
					isCheckout={ true }
					className={ '' }
				/>
			</SlotFillProvider>
		);
		expect(
			screen.queryByText( 'Change address' )
		).not.toBeInTheDocument();
		expect(
			screen.queryByText( 'Add an address for shipping options' )
		).not.toBeInTheDocument();
	} );
} );
