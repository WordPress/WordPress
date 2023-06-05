/**
 * External dependencies
 */
import { renderHook } from '@testing-library/react-hooks';
/**
 * Internal dependencies
 */
import { registerCheckoutFilters, applyCheckoutFilter } from '../';

describe( 'Checkout registry', () => {
	const filterName = 'loremIpsum';

	test( 'should return default value if there are no filters', () => {
		const value = 'Hello World';
		const { result: newValue } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
			} )
		);
		expect( newValue.current ).toBe( value );
	} );

	test( 'should return filtered value when a filter is registered', () => {
		const value = 'Hello World';
		registerCheckoutFilters( filterName, {
			[ filterName ]: ( val, extensions, args ) =>
				val.toUpperCase() + args.punctuationSign,
		} );
		const { result: newValue } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
				arg: {
					punctuationSign: '!',
				},
			} )
		);

		expect( newValue.current ).toBe( 'HELLO WORLD!' );
	} );

	test( 'should not return filtered value if validation failed', () => {
		const value = 'Hello World';
		registerCheckoutFilters( filterName, {
			[ filterName ]: ( val ) => val.toUpperCase(),
		} );
		const { result: newValue } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
				validation: ( val ) => ! val.includes( 'HELLO' ),
			} )
		);

		expect( newValue.current ).toBe( value );
	} );

	test( 'should catch filter errors if user is not an admin', () => {
		const spy = {};
		spy.console = jest
			.spyOn( console, 'error' )
			.mockImplementation( () => {} );

		const error = new Error( 'test error' );
		// We use this new filter name here to avoid return the cached value for the filter
		const filterNameThatThrows = 'throw';
		const value = 'Hello World';
		registerCheckoutFilters( filterNameThatThrows, {
			[ filterNameThatThrows ]: () => {
				throw error;
			},
		} );
		const { result: newValue } = renderHook( () =>
			applyCheckoutFilter( {
				filterName: filterNameThatThrows,
				defaultValue: value,
			} )
		);

		expect( spy.console ).toHaveBeenCalledWith( error );
		expect( newValue.current ).toBe( value );
		spy.console.mockRestore();
	} );

	it( 'should allow filters to be registered multiple times and return the correct value each time', () => {
		const value = 'Hello World';
		registerCheckoutFilters( filterName, {
			[ filterName ]: ( val, extensions, args ) =>
				val.toUpperCase() + args?.punctuationSign,
		} );
		const { result: newValue } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
				arg: {
					punctuationSign: '!',
				},
			} )
		);
		expect( newValue.current ).toBe( 'HELLO WORLD!' );
		registerCheckoutFilters( filterName, {
			[ filterName ]: ( val, extensions, args ) =>
				args?.punctuationSign +
				val.toUpperCase() +
				args?.punctuationSign,
		} );
		const { result: newValue2 } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
				arg: {
					punctuationSign: '!',
				},
			} )
		);
		expect( newValue2.current ).toBe( '!HELLO WORLD!' );
	} );
} );
