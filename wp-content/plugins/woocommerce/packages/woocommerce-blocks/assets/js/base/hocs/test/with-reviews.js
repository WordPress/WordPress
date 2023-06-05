// We need to disable the following eslint check as it's only applicable
// to testing-library/react not `react-test-renderer` used here
/* eslint-disable testing-library/await-async-query */
/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import withReviews from '../with-reviews';
import * as mockUtils from '../../../blocks/reviews/utils';
import * as mockBaseUtils from '../../utils/errors';

jest.mock( '../../../blocks/reviews/utils', () => ( {
	getSortArgs: () => ( {
		order: 'desc',
		orderby: 'date_gmt',
	} ),
	getReviews: jest.fn(),
} ) );

jest.mock( '../../utils/errors', () => ( {
	formatError: jest.fn(),
} ) );

const mockReviews = [
	{ reviewer: 'Alice', review: 'Lorem ipsum', rating: 2 },
	{ reviewer: 'Bob', review: 'Dolor sit amet', rating: 3 },
	{ reviewer: 'Carol', review: 'Consectetur adipiscing elit', rating: 5 },
];
const defaultArgs = {
	offset: 0,
	order: 'desc',
	orderby: 'date_gmt',
	per_page: 2,
	product_id: 1,
};
const TestComponent = withReviews( ( props ) => {
	return (
		<div
			error={ props.error }
			getReviews={ props.getReviews }
			appendReviews={ props.appendReviews }
			onChangeArgs={ props.onChangeArgs }
			isLoading={ props.isLoading }
			reviews={ props.reviews }
			totalReviews={ props.totalReviews }
		/>
	);
} );
const render = () => {
	return TestRenderer.create(
		<TestComponent
			attributes={ {} }
			order="desc"
			orderby="date_gmt"
			productId={ 1 }
			reviewsToDisplay={ 2 }
		/>
	);
};

describe( 'withReviews Component', () => {
	let renderer;
	afterEach( () => {
		mockUtils.getReviews.mockReset();
	} );

	describe( 'lifecycle events', () => {
		beforeEach( () => {
			mockUtils.getReviews
				.mockImplementationOnce( () =>
					Promise.resolve( {
						reviews: mockReviews.slice( 0, 2 ),
						totalReviews: mockReviews.length,
					} )
				)
				.mockImplementationOnce( () =>
					Promise.resolve( {
						reviews: mockReviews.slice( 2, 3 ),
						totalReviews: mockReviews.length,
					} )
				);
			renderer = render();
		} );

		it( 'getReviews is called on mount with default args', () => {
			const { getReviews } = mockUtils;

			expect( getReviews ).toHaveBeenCalledWith( defaultArgs );
			expect( getReviews ).toHaveBeenCalledTimes( 1 );
		} );

		it( 'getReviews is called on component update', () => {
			const { getReviews } = mockUtils;
			renderer.update(
				<TestComponent
					order="desc"
					orderby="date_gmt"
					productId={ 1 }
					reviewsToDisplay={ 3 }
				/>
			);

			expect( getReviews ).toHaveBeenNthCalledWith( 2, {
				...defaultArgs,
				offset: 2,
				per_page: 1,
			} );
			expect( getReviews ).toHaveBeenCalledTimes( 2 );
		} );
	} );

	describe( 'when the API returns product data', () => {
		beforeEach( () => {
			mockUtils.getReviews.mockImplementation( () =>
				Promise.resolve( {
					reviews: mockReviews.slice( 0, 2 ),
					totalReviews: mockReviews.length,
				} )
			);
			renderer = render();
		} );

		it( 'sets reviews based on API response', () => {
			const props = renderer.root.findByType( 'div' ).props;

			expect( props.error ).toBeNull();
			expect( props.isLoading ).toBe( false );
			expect( props.reviews ).toEqual( mockReviews.slice( 0, 2 ) );
			expect( props.totalReviews ).toEqual( mockReviews.length );
		} );
	} );

	describe( 'when the API returns an error', () => {
		const error = { message: 'There was an error.' };
		const getReviewsPromise = Promise.reject( error );
		const formattedError = { message: 'There was an error.', type: 'api' };

		beforeEach( () => {
			mockUtils.getReviews.mockImplementation( () => getReviewsPromise );
			mockBaseUtils.formatError.mockImplementation(
				() => formattedError
			);
			renderer = render();
		} );

		test( 'sets the error prop', async () => {
			await expect( () => getReviewsPromise() ).toThrow();

			const { formatError } = mockBaseUtils;
			const props = renderer.root.findByType( 'div' ).props;

			expect( formatError ).toHaveBeenCalledWith( error );
			expect( formatError ).toHaveBeenCalledTimes( 1 );
			expect( props.error ).toEqual( formattedError );
			expect( props.isLoading ).toBe( false );
			expect( props.reviews ).toEqual( [] );
		} );
	} );
} );
