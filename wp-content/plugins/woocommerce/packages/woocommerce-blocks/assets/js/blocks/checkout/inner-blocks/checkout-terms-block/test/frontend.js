/**
 * External dependencies
 */
import {
	render,
	findByLabelText,
	queryByLabelText,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import FrontendBlock from '../frontend';
import * as actionCreators from '../../../../../data/validation/actions';

jest.mock( '../../../../../data/validation/actions', () => {
	const actions = jest.requireActual(
		'../../../../../data/validation/actions'
	);
	return {
		...actions,
		clearValidationError: jest.fn().mockImplementation( ( errorId ) => {
			return actions.clearValidationError( errorId );
		} ),
	};
} );

describe( 'FrontendBlock', () => {
	it( 'Renders a checkbox if the checkbox prop is true', async () => {
		const { container } = render(
			<FrontendBlock
				checkbox={ true }
				text={ 'I agree to the terms and conditions' }
			/>
		);

		const checkbox = await findByLabelText(
			container,
			'I agree to the terms and conditions'
		);

		expect( checkbox ).toBeInTheDocument();
	} );

	it( 'Does not render a checkbox if the checkbox prop is false', async () => {
		const { container } = render(
			<FrontendBlock
				checkbox={ false }
				text={ 'I agree to the terms and conditions' }
			/>
		);

		const checkbox = queryByLabelText(
			container,
			'I agree to the terms and conditions'
		);

		expect( checkbox ).not.toBeInTheDocument();
	} );

	it( 'Clears any validation errors when the checkbox is checked', async () => {
		const { container } = render(
			<FrontendBlock
				checkbox={ true }
				text={ 'I agree to the terms and conditions' }
			/>
		);
		const checkbox = await findByLabelText(
			container,
			'I agree to the terms and conditions'
		);
		userEvent.click( checkbox );
		expect( actionCreators.clearValidationError ).toHaveBeenLastCalledWith(
			expect.stringMatching( /terms-and-conditions-\d/ )
		);
	} );
} );
