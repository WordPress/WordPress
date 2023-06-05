/**
 * Internal dependencies
 */
import { getIndexes } from '../utils';

describe( 'getIndexes', () => {
	describe( 'when on the first page', () => {
		test( 'indexes include the first pages available', () => {
			expect( getIndexes( 5, 1, 100 ) ).toEqual( {
				minIndex: 2,
				maxIndex: 6,
			} );
		} );

		test( 'indexes are null if there are 2 pages or less', () => {
			expect( getIndexes( 5, 1, 1 ) ).toEqual( {
				minIndex: null,
				maxIndex: null,
			} );
		} );
	} );

	describe( 'when on a page in the middle', () => {
		test( 'indexes include pages before and after the current page', () => {
			expect( getIndexes( 5, 50, 100 ) ).toEqual( {
				minIndex: 48,
				maxIndex: 52,
			} );
		} );
	} );

	describe( 'when on the last page', () => {
		test( 'indexes include the last pages available', () => {
			expect( getIndexes( 5, 100, 100 ) ).toEqual( {
				minIndex: 95,
				maxIndex: 99,
			} );
		} );
	} );
} );
