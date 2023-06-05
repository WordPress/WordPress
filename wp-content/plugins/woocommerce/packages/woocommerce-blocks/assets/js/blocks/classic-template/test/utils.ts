/**
 * Internal dependencies
 */
import { getTemplateDetailsBySlug } from '../utils';

const TEMPLATES = {
	'single-product': {
		title: 'Single Product Title',
		placeholder: 'Single Product Placeholder',
	},
	'archive-product': {
		title: 'Product Archive Title',
		placeholder: 'Product Archive Placeholder',
	},
	'taxonomy-product_cat': {
		title: 'Product Taxonomy Title',
		placeholder: 'Product Taxonomy Placeholder',
	},
	'taxonomy-product_attribute': {
		title: 'Product Attribute Title',
		placeholder: 'Product Attribute Placeholder',
	},
};

describe( 'getTemplateDetailsBySlug', function () {
	it( 'should return single-product object when given an exact match', () => {
		expect(
			getTemplateDetailsBySlug( 'single-product', TEMPLATES )
		).toBeTruthy();
		expect(
			getTemplateDetailsBySlug( 'single-product', TEMPLATES )
		).toStrictEqual( TEMPLATES[ 'single-product' ] );
	} );

	it( 'should return single-product object when given a partial match', () => {
		expect(
			getTemplateDetailsBySlug( 'single-product-hoodie', TEMPLATES )
		).toBeTruthy();
		expect(
			getTemplateDetailsBySlug( 'single-product-hoodie', TEMPLATES )
		).toStrictEqual( TEMPLATES[ 'single-product' ] );
	} );

	it( 'should return null object when given an incorrect match', () => {
		expect( getTemplateDetailsBySlug( 'void', TEMPLATES ) ).toBeNull();
	} );
} );
