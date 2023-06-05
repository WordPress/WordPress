/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import ShippingAddress from '@woocommerce/base-components/cart-checkout/totals/shipping/shipping-address';
import { CART_STORE_KEY, CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { dispatch } from '@wordpress/data';
import { previewCart } from '@woocommerce/resource-previews';

jest.mock( '@woocommerce/settings', () => {
	const originalModule = jest.requireActual( '@woocommerce/settings' );

	return {
		// eslint-disable-next-line @typescript-eslint/ban-ts-comment
		// @ts-ignore We know @woocommerce/settings is an object.
		...originalModule,
		getSetting: ( setting: string, ...rest: unknown[] ) => {
			if ( setting === 'localPickupEnabled' ) {
				return true;
			}
			if ( setting === 'collectableMethodIds' ) {
				return [ 'pickup_location' ];
			}
			return originalModule.getSetting( setting, ...rest );
		},
	};
} );
describe( 'ShippingAddress', () => {
	const testShippingAddress = {
		first_name: 'John',
		last_name: 'Doe',
		company: 'Automattic',
		address_1: '123 Main St',
		address_2: '',
		city: 'San Francisco',
		state: 'CA',
		postcode: '94107',
		country: 'US',
		phone: '555-555-5555',
	};

	it( 'renders ShippingLocation if user does not prefer collection', () => {
		render(
			<ShippingAddress
				showCalculator={ false }
				isShippingCalculatorOpen={ false }
				setIsShippingCalculatorOpen={ jest.fn() }
				shippingAddress={ testShippingAddress }
			/>
		);
		expect( screen.getByText( /Shipping to 94107/ ) ).toBeInTheDocument();
		expect(
			screen.queryByText( /Collection from/ )
		).not.toBeInTheDocument();
	} );
	it( 'renders PickupLocation if shopper prefers collection', async () => {
		dispatch( CHECKOUT_STORE_KEY ).setPrefersCollection( true );

		// Deselect the default selected rate and select pickup_location:1 rate.
		const currentlySelectedIndex =
			previewCart.shipping_rates[ 0 ].shipping_rates.findIndex(
				( rate ) => rate.selected
			);
		previewCart.shipping_rates[ 0 ].shipping_rates[
			currentlySelectedIndex
		].selected = false;
		const pickupRateIndex =
			previewCart.shipping_rates[ 0 ].shipping_rates.findIndex(
				( rate ) => rate.method_id === 'pickup_location'
			);
		previewCart.shipping_rates[ 0 ].shipping_rates[
			pickupRateIndex
		].selected = true;

		dispatch( CART_STORE_KEY ).receiveCart( previewCart );

		render(
			<ShippingAddress
				showCalculator={ false }
				isShippingCalculatorOpen={ false }
				setIsShippingCalculatorOpen={ jest.fn() }
				shippingAddress={ testShippingAddress }
			/>
		);
		expect(
			screen.getByText(
				/Collection from 123 Easy Street, New York, 12345/
			)
		).toBeInTheDocument();
	} );
} );
