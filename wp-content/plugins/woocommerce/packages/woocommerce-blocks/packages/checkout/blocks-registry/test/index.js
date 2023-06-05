/**
 * Internal dependencies
 */
import {
	getRegisteredBlocks,
	registerCheckoutBlock,
	innerBlockAreas,
} from '../index';

describe( 'checkout blocks registry', () => {
	const component = () => {
		return null;
	};

	describe( 'registerCheckoutBlock', () => {
		const invokeTest = ( blockName, options ) => () => {
			return registerCheckoutBlock( blockName, options );
		};
		it( 'throws an error when registered block is missing `blockName`', () => {
			expect(
				invokeTest( {
					metadata: {
						name: null,
						parent: innerBlockAreas.CHECKOUT_FIELDS,
					},
					component,
				} )
			).toThrowError( /blockName/ );
			expect(
				invokeTest( {
					metadata: {
						name: '',
						parent: innerBlockAreas.CHECKOUT_FIELDS,
					},
					component,
				} )
			).toThrowError( /blockName/ );
		} );
		it( 'throws an error when registered block is missing a valid parent', () => {
			expect(
				invokeTest( {
					metadata: {
						name: 'test/block-name',
						parent: [],
					},
					component,
				} )
			).toThrowError( /parent/ );
			expect(
				invokeTest( {
					metadata: {
						name: 'test/block-name',
						parent: 'invalid-parent',
					},
					component,
				} )
			).toThrowError( /parent/ );
			expect(
				invokeTest( {
					metadata: {
						name: 'test/block-name',
						parent: [
							'invalid-parent',
							innerBlockAreas.CHECKOUT_FIELDS,
						],
					},
					component,
				} )
			).not.toThrowError( /parent/ );
		} );
		it( 'throws an error when registered block is missing `component`', () => {
			expect(
				invokeTest( {
					metadata: {
						name: 'test/block-name',
						parent: innerBlockAreas.CHECKOUT_FIELDS,
					},
				} )
			).toThrowError( /component/ );
		} );
	} );

	describe( 'getRegisteredBlocks', () => {
		it( 'gets an empty array when checkout area has no registered blocks', () => {
			expect(
				getRegisteredBlocks( innerBlockAreas.CONTACT_INFORMATION )
			).toEqual( [] );
		} );
		it( 'gets an empty array when the area is not defined', () => {
			expect( getRegisteredBlocks( 'not-defined' ) ).toEqual( [] );
		} );
	} );
} );
