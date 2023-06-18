/**
 * External dependencies
 */
import { act, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { dispatch } from '@wordpress/data';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import { TotalsCoupon } from '..';

describe( 'TotalsCoupon', () => {
	it( "Shows a validation error when one is in the wc/store/validation data store and doesn't show one when there isn't", () => {
		const { rerender } = render( <TotalsCoupon instanceId={ 'coupon' } /> );
		const openCouponFormButton = screen.getByText( 'Add a coupon' );
		expect( openCouponFormButton ).toBeInTheDocument();
		userEvent.click( openCouponFormButton );
		expect(
			screen.queryByText( 'Invalid coupon code' )
		).not.toBeInTheDocument();

		const { setValidationErrors } = dispatch( VALIDATION_STORE_KEY );
		act( () => {
			setValidationErrors( {
				coupon: {
					hidden: false,
					message: 'Invalid coupon code',
				},
			} );
		} );
		rerender( <TotalsCoupon instanceId={ 'coupon' } /> );
		expect( screen.getByText( 'Invalid coupon code' ) ).toBeInTheDocument();
	} );
} );
