/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
/**
 * Internal dependencies
 */
import LocalPickupSelect from '..';

describe( 'LocalPickupSelect', () => {
	const TestComponent = ( {
		selectedOptionOverride = null,
		onSelectRateOverride = null,
	}: {
		selectedOptionOverride?: null | ( ( value: string ) => void );
		onSelectRateOverride?: null | ( ( value: string ) => void );
	} ) => (
		<LocalPickupSelect
			title="Package 1"
			setSelectedOption={ selectedOptionOverride || jest.fn() }
			selectedOption=""
			pickupLocations={ [
				{
					rate_id: '1',
					currency_code: 'USD',
					currency_decimal_separator: '.',
					currency_minor_unit: 2,
					currency_prefix: '$',
					currency_suffix: '',
					currency_thousand_separator: ',',
					currency_symbol: '$',
					name: 'Store 1',
					description: 'Store 1 description',
					delivery_time: '1 day',
					price: '0',
					taxes: '0',
					instance_id: 1,
					method_id: 'test_shipping:0',
					meta_data: [],
					selected: false,
				},
				{
					rate_id: '2',
					currency_code: 'USD',
					currency_decimal_separator: '.',
					currency_minor_unit: 2,
					currency_prefix: '$',
					currency_suffix: '',
					currency_thousand_separator: ',',
					currency_symbol: '$',
					name: 'Store 2',
					description: 'Store 2 description',
					delivery_time: '2 days',
					price: '0',
					taxes: '0',
					instance_id: 1,
					method_id: 'test_shipping:1',
					meta_data: [],
					selected: false,
				},
			] }
			onSelectRate={ onSelectRateOverride || jest.fn() }
			packageCount={ 1 }
			renderPickupLocation={ ( location ) => {
				return {
					value: `${ location.rate_id }`,
					onChange: jest.fn(),
					label: `${ location.name }`,
					description: `${ location.description }`,
				};
			} }
		/>
	);
	it( 'Does not render the title if only one package is present on the page', () => {
		render( <TestComponent /> );
		expect( screen.queryByText( 'Package 1' ) ).not.toBeInTheDocument();
	} );
	it( 'Does render the title if more than one package is present on the page', () => {
		const { rerender } = render(
			<div className="wc-block-components-local-pickup-select">
				<div className="wc-block-components-radio-control"></div>
			</div>
		);
		// Render twice so our component can check the DOM correctly.
		rerender(
			<>
				<div className="wc-block-components-local-pickup-select">
					<div className="wc-block-components-radio-control"></div>
				</div>
				<TestComponent />
			</>
		);
		rerender(
			<>
				<div className="wc-block-components-local-pickup-select">
					<div className="wc-block-components-radio-control"></div>
				</div>
				<TestComponent />
			</>
		);

		expect( screen.getByText( 'Package 1' ) ).toBeInTheDocument();
	} );
	it( 'Calls the correct functions when changing selected option', () => {
		const setSelectedOption = jest.fn();
		const onSelectRate = jest.fn();
		render(
			<TestComponent
				selectedOptionOverride={ setSelectedOption }
				onSelectRateOverride={ onSelectRate }
			/>
		);
		userEvent.click( screen.getByText( 'Store 2' ) );
		expect( setSelectedOption ).toHaveBeenLastCalledWith( '2' );
		expect( onSelectRate ).toHaveBeenLastCalledWith( '2' );
		userEvent.click( screen.getByText( 'Store 1' ) );
		expect( setSelectedOption ).toHaveBeenLastCalledWith( '1' );
		expect( onSelectRate ).toHaveBeenLastCalledWith( '1' );
	} );
} );
