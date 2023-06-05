/**
 * External dependencies
 */
import { act, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import { getValidityMessageForInput } from '../index';

describe( 'getValidityMessageForInput', () => {
	it( 'Returns nothing if the input is valid', async () => {
		render( <input type="text" data-testid="custom-input" /> );

		const textInputElement = ( await screen.getByTestId(
			'custom-input'
		) ) as HTMLInputElement;

		const validityMessage = getValidityMessageForInput(
			'Test',
			textInputElement
		);
		expect( validityMessage ).toBe( '' );
	} );
	it( 'Returns error message if a required input is empty', async () => {
		render( <input type="text" required data-testid="custom-input" /> );

		const textInputElement = ( await screen.getByTestId(
			'custom-input'
		) ) as HTMLInputElement;

		const validityMessage = getValidityMessageForInput(
			'Test',
			textInputElement
		);

		expect( validityMessage ).toBe( 'Please enter a valid test' );
	} );
	it( 'Returns a custom error if set, rather than a new message', async () => {
		render(
			<input
				type="text"
				required
				onChange={ ( event ) => {
					event.target.setCustomValidity( 'Custom error' );
				} }
				data-testid="custom-input"
			/>
		);

		const textInputElement = ( await screen.getByTestId(
			'custom-input'
		) ) as HTMLInputElement;

		await act( async () => {
			await userEvent.type( textInputElement, 'Invalid Value' );
		} );

		const validityMessage = getValidityMessageForInput(
			'Test',
			textInputElement
		);
		expect( validityMessage ).toBe( 'Custom error' );
	} );
} );
