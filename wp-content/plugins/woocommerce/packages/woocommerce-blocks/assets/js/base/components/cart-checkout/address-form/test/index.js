/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { CheckoutProvider } from '@woocommerce/base-context';
import { useCheckoutAddress } from '@woocommerce/base-context/hooks';

/**
 * Internal dependencies
 */
import AddressForm from '../address-form';

const renderInCheckoutProvider = ( ui, options = {} ) => {
	const Wrapper = ( { children } ) => {
		return <CheckoutProvider>{ children }</CheckoutProvider>;
	};
	return render( ui, { wrapper: Wrapper, ...options } );
};

// Countries used in testing addresses must be in the wcSettings global.
// See: tests/js/setup-globals.js
const primaryAddress = {
	country: 'United Kingdom',
	countryKey: 'GB',
	city: 'London',
	state: 'Greater London',
	postcode: 'ABCD',
};
const secondaryAddress = {
	country: 'Austria', // We use Austria because it doesn't have states.
	countryKey: 'AU',
	city: 'Vienna',
	postcode: 'DCBA',
};
const tertiaryAddress = {
	country: 'Canada', // We use Canada because it has a select for the state.
	countryKey: 'CA',
	city: 'Toronto',
	state: 'Ontario',
	postcode: 'EFGH',
};

const countryRegExp = /country/i;
const cityRegExp = /city/i;
const stateRegExp = /county|province|state/i;
const postalCodeRegExp = /postal code|postcode|zip/i;

const inputAddress = async ( {
	country = null,
	city = null,
	state = null,
	postcode = null,
} ) => {
	if ( country ) {
		const countryInput = screen.getByLabelText( countryRegExp );
		userEvent.type( countryInput, country + '{arrowdown}{enter}' );
	}
	if ( city ) {
		const cityInput = screen.getByLabelText( cityRegExp );
		userEvent.type( cityInput, city );
	}
	if ( state ) {
		const stateButton = screen.queryByRole( 'combobox', {
			name: stateRegExp,
		} );
		// State input might be a select or a text input.
		if ( stateButton ) {
			userEvent.click( stateButton );
			userEvent.click( screen.getByRole( 'option', { name: state } ) );
		} else {
			const stateInput = screen.getByLabelText( stateRegExp );
			userEvent.type( stateInput, state );
		}
	}
	if ( postcode ) {
		const postcodeInput = screen.getByLabelText( postalCodeRegExp );
		userEvent.type( postcodeInput, postcode );
	}
};

describe( 'AddressForm Component', () => {
	const WrappedAddressForm = ( { type } ) => {
		const { defaultAddressFields, setShippingAddress, shippingAddress } =
			useCheckoutAddress();

		return (
			<AddressForm
				type={ type }
				onChange={ setShippingAddress }
				values={ shippingAddress }
				fields={ Object.keys( defaultAddressFields ) }
			/>
		);
	};
	const ShippingFields = () => {
		const { shippingAddress } = useCheckoutAddress();

		return (
			<ul>
				{ Object.keys( shippingAddress ).map( ( key ) => (
					<li key={ key }>{ key + ': ' + shippingAddress[ key ] }</li>
				) ) }
			</ul>
		);
	};

	it( 'updates context value when interacting with form elements', () => {
		renderInCheckoutProvider(
			<>
				<WrappedAddressForm type="shipping" />
				<ShippingFields />
			</>
		);

		inputAddress( primaryAddress );

		expect( screen.getByText( /country/ ) ).toHaveTextContent(
			`country: ${ primaryAddress.countryKey }`
		);
		expect( screen.getByText( /city/ ) ).toHaveTextContent(
			`city: ${ primaryAddress.city }`
		);
		expect( screen.getByText( /state/ ) ).toHaveTextContent(
			`state: ${ primaryAddress.state }`
		);
		expect( screen.getByText( /postcode/ ) ).toHaveTextContent(
			`postcode: ${ primaryAddress.postcode }`
		);
	} );

	it( 'input fields update when changing the country', () => {
		renderInCheckoutProvider( <WrappedAddressForm type="shipping" /> );

		inputAddress( primaryAddress );

		// Verify correct labels are used.
		expect( screen.getByLabelText( /City/ ) ).toBeInTheDocument();
		expect( screen.getByLabelText( /County/ ) ).toBeInTheDocument();
		expect( screen.getByLabelText( /Postcode/ ) ).toBeInTheDocument();

		inputAddress( secondaryAddress );

		// Verify state input has been removed.
		expect( screen.queryByText( stateRegExp ) ).not.toBeInTheDocument();

		inputAddress( tertiaryAddress );

		// Verify postal code input label changed.
		expect( screen.getByLabelText( /Postal code/ ) ).toBeInTheDocument();
	} );

	it( 'input values are reset after changing the country', () => {
		renderInCheckoutProvider( <WrappedAddressForm type="shipping" /> );

		inputAddress( secondaryAddress );
		// Only update `country` to verify other values are reset.
		inputAddress( { country: primaryAddress.country } );
		expect( screen.getByLabelText( stateRegExp ).value ).toBe( '' );

		// Repeat the test with an address which has a select for the state.
		inputAddress( tertiaryAddress );
		inputAddress( { country: primaryAddress.country } );
		expect( screen.getByLabelText( stateRegExp ).value ).toBe( '' );
	} );
} );
