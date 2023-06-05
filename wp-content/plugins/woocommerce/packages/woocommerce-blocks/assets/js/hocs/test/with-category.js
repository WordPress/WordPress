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
import withCategory from '../with-category';
import * as mockBaseUtils from '../../base/utils/errors';

jest.mock( '@woocommerce/editor-components/utils', () => ( {
	getCategory: jest.fn(),
} ) );

jest.mock( '../../base/utils/errors', () => ( {
	formatError: jest.fn(),
} ) );

const mockCategory = { name: 'Clothing' };
const attributes = { categoryId: 1 };
const TestComponent = withCategory( ( props ) => {
	return (
		<div
			error={ props.error }
			getCategory={ props.getCategory }
			isLoading={ props.isLoading }
			category={ props.category }
		/>
	);
} );
const render = () => {
	return TestRenderer.create( <TestComponent attributes={ attributes } /> );
};

describe( 'withCategory Component', () => {
	let renderer;
	afterEach( () => {
		mockUtils.getCategory.mockReset();
	} );

	describe( 'lifecycle events', () => {
		beforeEach( () => {
			mockUtils.getCategory.mockImplementation( () => Promise.resolve() );
			renderer = render();
		} );

		it( 'getCategory is called on mount with passed in category id', () => {
			const { getCategory } = mockUtils;

			expect( getCategory ).toHaveBeenCalledWith( attributes.categoryId );
			expect( getCategory ).toHaveBeenCalledTimes( 1 );
		} );

		it( 'getCategory is called on component update', () => {
			const { getCategory } = mockUtils;
			const newAttributes = { ...attributes, categoryId: 2 };
			renderer.update( <TestComponent attributes={ newAttributes } /> );

			expect( getCategory ).toHaveBeenNthCalledWith(
				2,
				newAttributes.categoryId
			);
			expect( getCategory ).toHaveBeenCalledTimes( 2 );
		} );

		it( 'getCategory is hooked to the prop', () => {
			const { getCategory } = mockUtils;
			const props = renderer.root.findByType( 'div' ).props;

			props.getCategory();

			expect( getCategory ).toHaveBeenCalledTimes( 2 );
		} );
	} );

	describe( 'when the API returns category data', () => {
		beforeEach( () => {
			mockUtils.getCategory.mockImplementation( ( categoryId ) =>
				Promise.resolve( { ...mockCategory, id: categoryId } )
			);
			renderer = render();
		} );

		it( 'sets the category props', () => {
			const props = renderer.root.findByType( 'div' ).props;

			expect( props.error ).toBeNull();
			expect( typeof props.getCategory ).toBe( 'function' );
			expect( props.isLoading ).toBe( false );
			expect( props.category ).toEqual( {
				...mockCategory,
				id: attributes.categoryId,
			} );
		} );
	} );

	describe( 'when the API returns an error', () => {
		const error = { message: 'There was an error.' };
		const getCategoryPromise = Promise.reject( error );
		const formattedError = { message: 'There was an error.', type: 'api' };

		beforeEach( () => {
			mockUtils.getCategory.mockImplementation(
				() => getCategoryPromise
			);
			mockBaseUtils.formatError.mockImplementation(
				() => formattedError
			);
			renderer = render();
		} );

		test( 'sets the error prop', async () => {
			await expect( () => getCategoryPromise() ).toThrow();

			const { formatError } = mockBaseUtils;
			const props = renderer.root.findByType( 'div' ).props;

			expect( formatError ).toHaveBeenCalledWith( error );
			expect( formatError ).toHaveBeenCalledTimes( 1 );
			expect( props.error ).toEqual( formattedError );
			expect( typeof props.getCategory ).toBe( 'function' );
			expect( props.isLoading ).toBe( false );
			expect( props.category ).toBeNull();
		} );
	} );
} );
