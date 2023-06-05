// We need to disable the following eslint check as it's only applicable
// to testing-library/react not `react-test-renderer` used here
/* eslint-disable testing-library/await-async-query */
/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';
import * as mockUtils from '@woocommerce/editor-components/utils';

/**
 * Internal dependencies
 */
import withCategories from '../with-categories';
import * as mockBaseUtils from '../../base/utils/errors';

jest.mock( '@woocommerce/editor-components/utils', () => ( {
	getCategories: jest.fn(),
} ) );

jest.mock( '../../base/utils/errors', () => ( {
	formatError: jest.fn(),
} ) );

const mockCategories = [
	{ id: 1, name: 'Clothing' },
	{ id: 2, name: 'Food' },
];
const TestComponent = withCategories( ( props ) => {
	return (
		<div
			error={ props.error }
			isLoading={ props.isLoading }
			categories={ props.categories }
		/>
	);
} );
const render = () => {
	return TestRenderer.create( <TestComponent /> );
};

describe( 'withCategories Component', () => {
	let renderer;
	afterEach( () => {
		mockUtils.getCategories.mockReset();
	} );

	describe( 'lifecycle events', () => {
		beforeEach( () => {
			mockUtils.getCategories.mockImplementation( () =>
				Promise.resolve()
			);
			renderer = render();
		} );

		it( 'getCategories is called on mount', () => {
			const { getCategories } = mockUtils;
			expect( getCategories ).toHaveBeenCalledTimes( 1 );
		} );
	} );

	describe( 'when the API returns categories data', () => {
		beforeEach( () => {
			mockUtils.getCategories.mockImplementation( () =>
				Promise.resolve( mockCategories )
			);
			renderer = render();
		} );

		it( 'sets the categories props', () => {
			const props = renderer.root.findByType( 'div' ).props;

			expect( props.error ).toBeNull();
			expect( props.isLoading ).toBe( false );
			expect( props.categories ).toEqual( mockCategories );
		} );
	} );

	describe( 'when the API returns an error', () => {
		const error = { message: 'There was an error.' };
		const getCategoriesPromise = Promise.reject( error );
		const formattedError = { message: 'There was an error.', type: 'api' };

		beforeEach( () => {
			mockUtils.getCategories.mockImplementation(
				() => getCategoriesPromise
			);
			mockBaseUtils.formatError.mockImplementation(
				() => formattedError
			);
			renderer = render();
		} );

		test( 'sets the error prop', async () => {
			await expect( () => getCategoriesPromise() ).toThrow();

			const { formatError } = mockBaseUtils;
			const props = renderer.root.findByType( 'div' ).props;

			expect( formatError ).toHaveBeenCalledWith( error );
			expect( formatError ).toHaveBeenCalledTimes( 1 );
			expect( props.error ).toEqual( formattedError );
			expect( props.isLoading ).toBe( false );
			expect( props.categories ).toEqual( [] );
		} );
	} );
} );
