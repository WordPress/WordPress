/**
 * External dependencies
 */
import { act, render, screen } from '@testing-library/react';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { dispatch, select } from '@wordpress/data';
import userEvent from '@testing-library/user-event';
import { useState } from '@wordpress/element';
import * as wpData from '@wordpress/data';

/**
 * Internal dependencies
 */
import { __ValidatedTexInputWithoutId as ValidatedTextInput } from '../validated-text-input';

jest.mock( '@wordpress/data', () => ( {
	__esModule: true,
	...jest.requireActual( '@wordpress/data' ),
	useDispatch: jest.fn().mockImplementation( ( args ) => {
		return jest.requireActual( '@wordpress/data' ).useDispatch( args );
	} ),
} ) );

describe( 'ValidatedTextInput', () => {
	it( 'Removes related validation error on change', async () => {
		render(
			<ValidatedTextInput
				instanceId={ '0' }
				accept={ 'image/*' }
				onChange={ () => void 0 }
				value={ 'Test' }
				id={ 'test-input' }
				label={ 'Test Input' }
			/>
		);

		await act( () =>
			dispatch( VALIDATION_STORE_KEY ).setValidationErrors( {
				'test-input': {
					message: 'Error message',
					hidden: false,
				},
			} )
		);

		await expect(
			select( VALIDATION_STORE_KEY ).getValidationError( 'test-input' )
		).not.toBe( undefined );
		const textInputElement = await screen.getByLabelText( 'Test Input' );
		await userEvent.type( textInputElement, 'New value' );
		await expect(
			select( VALIDATION_STORE_KEY ).getValidationError( 'test-input' )
		).toBe( undefined );
	} );
	it( 'Hides related validation error on change when id is not specified', async () => {
		render(
			<ValidatedTextInput
				instanceId={ '1' }
				accept={ 'image/*' }
				onChange={ () => void 0 }
				value={ 'Test' }
				label={ 'Test Input' }
			/>
		);

		await act( () =>
			dispatch( VALIDATION_STORE_KEY ).setValidationErrors( {
				'textinput-1': {
					message: 'Error message',
					hidden: false,
				},
			} )
		);
		await expect(
			select( VALIDATION_STORE_KEY ).getValidationError( 'textinput-1' )
		).not.toBe( undefined );
		const textInputElement = await screen.getByLabelText( 'Test Input' );
		await userEvent.type( textInputElement, 'New value' );
		await expect(
			select( VALIDATION_STORE_KEY ).getValidationError( 'textinput-1' )
		).toBe( undefined );
	} );
	it( 'Displays a passed error message', async () => {
		render(
			<ValidatedTextInput
				instanceId={ '2' }
				accept={ 'image/*' }
				onChange={ () => void 0 }
				value={ 'Test' }
				label={ 'Test Input' }
				errorMessage={ 'Custom error message' }
			/>
		);
		await act( () =>
			dispatch( VALIDATION_STORE_KEY ).setValidationErrors( {
				'textinput-2': {
					message: 'Error message in data store',
					hidden: false,
				},
			} )
		);
		const customErrorMessageElement = await screen.getByText(
			'Custom error message'
		);
		expect(
			screen.queryByText( 'Error message in data store' )
		).not.toBeInTheDocument();
		await expect( customErrorMessageElement ).toBeInTheDocument();
	} );
	it( 'Displays an error message from the data store', async () => {
		render(
			<ValidatedTextInput
				instanceId={ '3' }
				accept={ 'image/*' }
				onChange={ () => void 0 }
				value={ 'Test' }
				label={ 'Test Input' }
			/>
		);
		await act( () =>
			dispatch( VALIDATION_STORE_KEY ).setValidationErrors( {
				'textinput-3': {
					message: 'Error message 3',
					hidden: false,
				},
			} )
		);
		const errorMessageElement = await screen.getByText( 'Error message 3' );
		await expect( errorMessageElement ).toBeInTheDocument();
	} );
	it( 'Runs custom validation on the input', async () => {
		const TestComponent = () => {
			const [ inputValue, setInputValue ] = useState( 'Test' );
			return (
				<ValidatedTextInput
					instanceId={ '4' }
					id={ 'test-input' }
					onChange={ ( value ) => setInputValue( value ) }
					value={ inputValue }
					label={ 'Test Input' }
					customValidation={ ( inputObject ) => {
						return inputObject.value === 'Valid Value';
					} }
				/>
			);
		};
		render( <TestComponent /> );

		const textInputElement = await screen.getByLabelText( 'Test Input' );
		await userEvent.type( textInputElement, 'Invalid Value' );
		await expect(
			select( VALIDATION_STORE_KEY ).getValidationError( 'test-input' )
		).not.toBe( undefined );
		await userEvent.type( textInputElement, '{selectall}{del}Valid Value' );
		await expect( textInputElement.value ).toBe( 'Valid Value' );
		await expect(
			select( VALIDATION_STORE_KEY ).getValidationError( 'test-input' )
		).toBe( undefined );
	} );
	it( 'Shows a custom error message for an invalid required input', async () => {
		const TestComponent = () => {
			const [ inputValue, setInputValue ] = useState( '' );
			return (
				<ValidatedTextInput
					instanceId={ '5' }
					id={ 'test-input' }
					onChange={ ( value ) => setInputValue( value ) }
					value={ inputValue }
					label={ 'Test Input' }
					required={ true }
				/>
			);
		};
		render( <TestComponent /> );
		const textInputElement = await screen.getByLabelText( 'Test Input' );
		await userEvent.type( textInputElement, 'test' );
		await userEvent.type( textInputElement, '{selectall}{del}' );
		await textInputElement.blur();
		await expect(
			screen.queryByText( 'Please enter a valid test input' )
		).not.toBeNull();
	} );
	describe( 'correctly validates on mount', () => {
		it( 'validates when focusOnMount is true and validateOnMount is not set', async () => {
			const setValidationErrors = jest.fn();
			wpData.useDispatch.mockImplementation( ( storeName: string ) => {
				if ( storeName === VALIDATION_STORE_KEY ) {
					return {
						...jest
							.requireActual( '@wordpress/data' )
							.useDispatch( storeName ),
						setValidationErrors,
					};
				}
				return jest
					.requireActual( '@wordpress/data' )
					.useDispatch( storeName );
			} );

			const TestComponent = () => {
				const [ inputValue, setInputValue ] = useState( '' );
				return (
					<ValidatedTextInput
						instanceId={ '6' }
						id={ 'test-input' }
						onChange={ ( value ) => setInputValue( value ) }
						value={ inputValue }
						label={ 'Test Input' }
						required={ true }
						focusOnMount={ true }
					/>
				);
			};
			await render( <TestComponent /> );
			const textInputElement = await screen.getByLabelText(
				'Test Input'
			);
			await expect( textInputElement ).toHaveFocus();
			await expect( setValidationErrors ).toHaveBeenCalledWith( {
				'test-input': {
					message: 'Please enter a valid test input',
					hidden: true,
				},
			} );
		} );
		it( 'validates when focusOnMount is false, regardless of validateOnMount value', async () => {
			const setValidationErrors = jest.fn();
			wpData.useDispatch.mockImplementation( ( storeName: string ) => {
				if ( storeName === VALIDATION_STORE_KEY ) {
					return {
						...jest
							.requireActual( '@wordpress/data' )
							.useDispatch( storeName ),
						setValidationErrors,
					};
				}
				return jest
					.requireActual( '@wordpress/data' )
					.useDispatch( storeName );
			} );

			const TestComponent = ( { validateOnMount = false } ) => {
				const [ inputValue, setInputValue ] = useState( '' );
				return (
					<ValidatedTextInput
						instanceId={ '6' }
						id={ 'test-input' }
						onChange={ ( value ) => setInputValue( value ) }
						value={ inputValue }
						label={ 'Test Input' }
						required={ true }
						focusOnMount={ true }
						validateOnMount={ validateOnMount }
					/>
				);
			};
			const { rerender } = await render( <TestComponent /> );
			const textInputElement = await screen.getByLabelText(
				'Test Input'
			);
			await expect( textInputElement ).toHaveFocus();
			await expect( setValidationErrors ).not.toHaveBeenCalled();

			await rerender( <TestComponent validateOnMount={ true } /> );
			await expect( textInputElement ).toHaveFocus();
			await expect( setValidationErrors ).not.toHaveBeenCalled();
		} );
		it( 'does not validate when validateOnMount is false and focusOnMount is true', async () => {
			const setValidationErrors = jest.fn();
			wpData.useDispatch.mockImplementation( ( storeName: string ) => {
				if ( storeName === VALIDATION_STORE_KEY ) {
					return {
						...jest
							.requireActual( '@wordpress/data' )
							.useDispatch( storeName ),
						setValidationErrors,
					};
				}
				return jest
					.requireActual( '@wordpress/data' )
					.useDispatch( storeName );
			} );

			const TestComponent = () => {
				const [ inputValue, setInputValue ] = useState( '' );
				return (
					<ValidatedTextInput
						instanceId={ '6' }
						id={ 'test-input' }
						onChange={ ( value ) => setInputValue( value ) }
						value={ inputValue }
						label={ 'Test Input' }
						required={ true }
						focusOnMount={ true }
						validateOnMount={ false }
					/>
				);
			};
			await render( <TestComponent /> );
			const textInputElement = await screen.getByLabelText(
				'Test Input'
			);
			await expect( textInputElement ).toHaveFocus();
			await expect( setValidationErrors ).not.toHaveBeenCalled();
		} );
	} );
} );
