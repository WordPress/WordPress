/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import { CART_STORE_KEY, CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { dispatch } from '@wordpress/data';
import { previewCart } from '@woocommerce/resource-previews';
import PickupLocation from '@woocommerce/base-components/cart-checkout/pickup-location';

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
describe( 'PickupLocation', () => {
	it( `renders an address if one is set in the method's metadata`, async () => {
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

		render( <PickupLocation /> );
		expect(
			screen.getByText(
				/Collection from 123 Easy Street, New York, 12345/
			)
		).toBeInTheDocument();
	} );
	it( 'renders the method name if address is not in metadata', async () => {
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
				( rate ) => rate.rate_id === 'pickup_location:2'
			);
		previewCart.shipping_rates[ 0 ].shipping_rates[
			pickupRateIndex
		].selected = true;

		// Set the pickup_location metadata value to an empty string in the selected pickup rate.
		const addressKeyIndex = previewCart.shipping_rates[ 0 ].shipping_rates[
			pickupRateIndex
		].meta_data.findIndex(
			( metaData ) => metaData.key === 'pickup_address'
		);
		previewCart.shipping_rates[ 0 ].shipping_rates[
			pickupRateIndex
		].meta_data[ addressKeyIndex ].value = '';

		dispatch( CART_STORE_KEY ).receiveCart( previewCart );

		render( <PickupLocation /> );
		expect(
			screen.getByText( /Collection from Local pickup/ )
		).toBeInTheDocument();
	} );
} );
