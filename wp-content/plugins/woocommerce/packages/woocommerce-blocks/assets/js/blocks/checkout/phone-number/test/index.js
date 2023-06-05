/**
 * External dependencies
 */

import { render, findByLabelText } from '@testing-library/react';

/**
 * Internal dependencies
 */
import PhoneNumber from '../index';

describe( 'Phone number', () => {
	it( 'Renders an input field with type tel', async () => {
		const { container } = render(
			<PhoneNumber
				id={ 'shipping-phone' }
				isRequired={ true }
				onChange={ () => null }
				value={ '' }
			/>
		);
		const input = await findByLabelText( container, 'Phone' );
		expect( input.getAttribute( 'type' ) ).toEqual( 'tel' );
	} );
	it( 'Renders (optional) in the label if the field is not marked as required', async () => {
		const { container } = render(
			<PhoneNumber
				id={ 'shipping-phone' }
				isRequired={ false }
				onChange={ () => null }
				value={ '' }
			/>
		);
		const input = await findByLabelText( container, 'Phone (optional)' );
		expect( input ).toBeTruthy();
	} );
} );
