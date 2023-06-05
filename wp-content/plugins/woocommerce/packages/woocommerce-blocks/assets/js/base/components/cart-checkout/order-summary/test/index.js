/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import { previewCart } from '@woocommerce/resource-previews';

/**
 * Internal dependencies
 */
import OrderSummary from '../index';

jest.mock( '@woocommerce/base-context', () => ( {
	...jest.requireActual( '@woocommerce/base-context' ),
	useContainerWidthContext: () => ( {
		isLarge: true,
		hasContainerWidth: true,
	} ),
} ) );

describe( 'Order Summary', () => {
	it( 'renders correct cart line subtotal when currency has 0 decimals', async () => {
		render(
			<OrderSummary
				cartItems={ [
					{
						...previewCart.items[ 0 ],
						totals: {
							...previewCart.items[ 0 ].totals,
							// Change price format so there are no decimals.
							currency_minor_unit: 0,
							currency_prefix: '',
							currency_suffix: '€',
							line_subtotal: '16',
							line_total: '18',
						},
					},
				] }
			/>
		);

		expect( screen.getByText( '16€' ) ).toBeTruthy();
	} );
} );
