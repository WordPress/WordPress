/**
 * Internal dependencies
 */
import {
	getRegisteredBlockComponents,
	registerBlockComponent,
	registerInnerBlock,
	getRegisteredInnerBlocks,
} from '../index';

describe( 'blocks registry', () => {
	const context = '@woocommerce/all-products';
	const blockName = '@woocommerce-extension/price-level';
	const component = () => {
		return null;
	};

	describe( 'registerBlockComponent', () => {
		const invokeTest = ( args ) => () => {
			return registerBlockComponent( args );
		};
		it( 'throws an error when registered block is missing `blockName`', () => {
			expect( invokeTest( { context, blockName: null } ) ).toThrowError(
				/blockName/
			);
		} );
		it( 'throws an error when registered block is missing `component`', () => {
			expect(
				invokeTest( { context, blockName, component: null } )
			).toThrowError( /component/ );
		} );
	} );

	describe( 'getRegisteredBlockComponents', () => {
		it( 'gets an empty object when context has no inner blocks', () => {
			expect(
				getRegisteredBlockComponents( '@woocommerce/all-products' )
			).toEqual( {} );
		} );
		it( 'gets a block that was successfully registered', () => {
			registerBlockComponent( { context, blockName, component } );
			expect(
				getRegisteredBlockComponents( '@woocommerce/all-products' )
			).toEqual( { [ blockName ]: component } );
		} );
	} );

	describe( 'registerInnerBlock (deprecated)', () => {
		const invokeTest = ( args ) => () => {
			registerInnerBlock( args );
		};

		it( 'throws an error when registered block is missing `main`', () => {
			const options = { main: null };
			expect( invokeTest( options ) ).toThrowError( /main/ );
			expect( console ).toHaveWarned();
		} );
		it( 'throws an error when registered block is missing `blockName`', () => {
			const options = { main: context, blockName: null };
			expect( invokeTest( options ) ).toThrowError( /blockName/ );
		} );
		it( 'throws an error when registered block is missing `component`', () => {
			const options = { main: context, blockName, component: null };
			expect( invokeTest( options ) ).toThrowError( /component/ );
		} );
	} );

	describe( 'getRegisteredInnerBlocks (deprecated)', () => {
		it( 'gets an empty object when parent has no inner blocks', () => {
			expect(
				getRegisteredInnerBlocks( '@woocommerce/test-parent' )
			).toEqual( {} );
			expect( console ).toHaveWarned();
		} );
		it( 'gets a block that was successfully registered', () => {
			registerBlockComponent( { context, blockName, component } );
			expect(
				getRegisteredInnerBlocks( '@woocommerce/all-products' )
			).toEqual( {
				[ blockName ]: component,
			} );
		} );
	} );
} );
