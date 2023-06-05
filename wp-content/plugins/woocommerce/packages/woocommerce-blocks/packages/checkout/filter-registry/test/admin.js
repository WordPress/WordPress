/**
 * External dependencies
 */
import { renderHook } from '@testing-library/react-hooks';
/**
 * Internal dependencies
 */
import { registerCheckoutFilters, applyCheckoutFilter } from '../';

jest.mock( '@woocommerce/settings', () => {
	const originalModule = jest.requireActual( '@woocommerce/settings' );
	return {
		// @ts-ignore We know @woocommerce/settings is an object.
		...originalModule,
		CURRENT_USER_IS_ADMIN: true,
	};
} );

describe( 'Checkout registry (as admin user)', () => {
	test( 'should throw if the filter throws and user is an admin', () => {
		const filterName = 'ErrorTestFilter';
		const value = 'Hello World';
		registerCheckoutFilters( filterName, {
			[ filterName ]: () => {
				throw new Error( 'test error' );
			},
		} );

		const { result } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
			} )
		);
		expect( result.error ).toEqual( Error( 'test error' ) );
	} );

	test( 'should throw if validation throws and user is an admin', () => {
		const filterName = 'ValidationTestFilter';
		const value = 'Hello World';
		registerCheckoutFilters( filterName, {
			[ filterName ]: ( val ) => val,
		} );
		const { result } = renderHook( () =>
			applyCheckoutFilter( {
				filterName,
				defaultValue: value,
				validation: () => {
					throw Error( 'validation error' );
				},
			} )
		);
		expect( result.error ).toEqual( Error( 'validation error' ) );
	} );
} );
