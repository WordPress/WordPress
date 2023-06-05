/**
 * Internal dependencies
 */
import { getImageSrcFromProduct, getImageIdFromProduct } from '../products';

describe( 'getImageSrcFromProduct', () => {
	test( 'returns first image src', () => {
		const imageSrc = getImageSrcFromProduct( {
			images: [ { src: 'foo.jpg' } ],
		} );

		expect( imageSrc ).toBe( 'foo.jpg' );
	} );

	test( 'returns empty string if no product was provided', () => {
		const imageSrc = getImageSrcFromProduct();

		expect( imageSrc ).toBe( '' );
	} );

	test( 'returns empty string if product is empty', () => {
		const imageSrc = getImageSrcFromProduct( {} );

		expect( imageSrc ).toBe( '' );
	} );

	test( 'returns empty string if product has no images', () => {
		const imageSrc = getImageSrcFromProduct( { images: null } );

		expect( imageSrc ).toBe( '' );
	} );

	test( 'returns empty string if product has 0 images', () => {
		const imageSrc = getImageSrcFromProduct( { images: [] } );

		expect( imageSrc ).toBe( '' );
	} );

	test( 'returns empty string if product image has no src attribute', () => {
		const imageSrc = getImageSrcFromProduct( { images: [ {} ] } );

		expect( imageSrc ).toBe( '' );
	} );
} );

describe( 'getImageIdFromProduct', () => {
	test( 'returns first image id', () => {
		const imageUrl = getImageIdFromProduct( {
			images: [ { id: 123 } ],
		} );

		expect( imageUrl ).toBe( 123 );
	} );

	test( 'returns 0 if no product was provided', () => {
		const imageUrl = getImageIdFromProduct();

		expect( imageUrl ).toBe( 0 );
	} );

	test( 'returns 0 if product is empty', () => {
		const imageUrl = getImageIdFromProduct( {} );

		expect( imageUrl ).toBe( 0 );
	} );

	test( 'returns 0 if product has no images', () => {
		const imageUrl = getImageIdFromProduct( { images: null } );

		expect( imageUrl ).toBe( 0 );
	} );

	test( 'returns 0 if product has 0 images', () => {
		const imageUrl = getImageIdFromProduct( { images: [] } );

		expect( imageUrl ).toBe( 0 );
	} );

	test( 'returns 0 if product image has no src attribute', () => {
		const imageUrl = getImageIdFromProduct( { images: [ {} ] } );

		expect( imageUrl ).toBe( 0 );
	} );
} );
