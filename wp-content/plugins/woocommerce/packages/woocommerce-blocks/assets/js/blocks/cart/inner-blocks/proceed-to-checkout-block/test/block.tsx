/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import { registerCheckoutFilters } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import Block from '../block';

describe( 'Proceed to checkout block', () => {
	it( 'allows the text to be filtered', () => {
		registerCheckoutFilters( 'test-extension', {
			proceedToCheckoutButtonLabel: () => {
				return 'Proceed to step two';
			},
		} );
		render(
			<Block checkoutPageId={ 0 } buttonLabel={ '' } className={ '' } />
		);
		expect( screen.getByText( 'Proceed to step two' ) ).toBeInTheDocument();
	} );
	it( 'allows the link to be filtered', () => {
		registerCheckoutFilters( 'test-extension', {
			proceedToCheckoutButtonLink: () => {
				return 'https://woocommerce.com';
			},
		} );
		render(
			<Block checkoutPageId={ 0 } buttonLabel={ '' } className={ '' } />
		);
		const button = screen.getByText( 'Proceed to Checkout' );
		const link = button.closest( 'a' );
		expect( link?.href ).toBe( 'https://woocommerce.com/' );
	} );
	it( 'does not allow incorrect types to be applied to either button label or button link', () => {
		registerCheckoutFilters( 'test-extension', {
			proceedToCheckoutButtonLabel: () => {
				return 123;
			},
			proceedToCheckoutButtonLink: () => {
				return 123;
			},
		} );
		render(
			<Block checkoutPageId={ 0 } buttonLabel={ '' } className={ '' } />
		);
		//@todo When https://github.com/WordPress/gutenberg/issues/22850 is complete use that new matcher here for more specific error message assertion.
		expect( console ).toHaveErrored();
	} );
} );
