/**
 * External dependencies
 */
import { ProductResponseAttributeItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import {
	getAttributes,
	getVariationAttributes,
	getVariationsMatchingSelectedAttributes,
	getVariationMatchingSelectedAttributes,
	getActiveSelectControlOptions,
	getDefaultAttributes,
} from '../utils';

const rawAttributeData: ProductResponseAttributeItem[] = [
	{
		id: 1,
		name: 'Color',
		taxonomy: 'pa_color',
		has_variations: true,
		terms: [
			{
				id: 22,
				name: 'Blue',
				slug: 'blue',
				default: true,
			},
			{
				id: 23,
				name: 'Green',
				slug: 'green',
				default: false,
			},
			{
				id: 24,
				name: 'Red',
				slug: 'red',
				default: false,
			},
		],
	},
	{
		id: 0,
		name: 'Logo',
		taxonomy: 'pa_logo',
		has_variations: true,
		terms: [
			{
				id: 0,
				name: 'Yes',
				slug: 'Yes',
				default: true,
			},
			{
				id: 0,
				name: 'No',
				slug: 'No',
				default: false,
			},
		],
	},
	{
		id: 0,
		name: 'Non-variable attribute',
		taxonomy: 'pa_non-variable-attribute',
		has_variations: false,
		terms: [
			{
				id: 0,
				name: 'Test',
				slug: 'Test',
				default: false,
			},
			{
				id: 0,
				name: 'Test 2',
				slug: 'Test 2',
				default: false,
			},
		],
	},
];

const rawVariations = [
	{
		id: 35,
		attributes: [
			{
				name: 'Color',
				value: 'blue',
			},
			{
				name: 'Logo',
				value: 'Yes',
			},
		],
	},
	{
		id: 28,
		attributes: [
			{
				name: 'Color',
				value: 'red',
			},
			{
				name: 'Logo',
				value: 'No',
			},
		],
	},
	{
		id: 29,
		attributes: [
			{
				name: 'Color',
				value: 'green',
			},
			{
				name: 'Logo',
				value: 'No',
			},
		],
	},
	{
		id: 30,
		attributes: [
			{
				name: 'Color',
				value: 'blue',
			},
			{
				name: 'Logo',
				value: 'No',
			},
		],
	},
];

const formattedAttributes = {
	Color: {
		id: 1,
		name: 'Color',
		taxonomy: 'pa_color',
		has_variations: true,
		terms: [
			{
				id: 22,
				name: 'Blue',
				slug: 'blue',
				default: true,
			},
			{
				id: 23,
				name: 'Green',
				slug: 'green',
				default: false,
			},
			{
				id: 24,
				name: 'Red',
				slug: 'red',
				default: false,
			},
		],
	},
	Size: {
		id: 2,
		name: 'Size',
		taxonomy: 'pa_size',
		has_variations: true,
		terms: [
			{
				id: 25,
				name: 'Large',
				slug: 'large',
				default: false,
			},
			{
				id: 26,
				name: 'Medium',
				slug: 'medium',
				default: true,
			},
			{
				id: 27,
				name: 'Small',
				slug: 'small',
				default: false,
			},
		],
	},
};

describe( 'Testing utils', () => {
	describe( 'Testing getAttributes()', () => {
		it( 'returns empty object if there are no attributes', () => {
			const attributes = getAttributes( null );
			expect( attributes ).toStrictEqual( {} );
		} );
		it( 'returns list of attributes when given valid data', () => {
			const attributes = getAttributes( rawAttributeData );
			expect( attributes ).toStrictEqual( {
				Color: {
					id: 1,
					name: 'Color',
					taxonomy: 'pa_color',
					has_variations: true,
					terms: [
						{
							id: 22,
							name: 'Blue',
							slug: 'blue',
							default: true,
						},
						{
							id: 23,
							name: 'Green',
							slug: 'green',
							default: false,
						},
						{
							id: 24,
							name: 'Red',
							slug: 'red',
							default: false,
						},
					],
				},
				Logo: {
					id: 0,
					name: 'Logo',
					taxonomy: 'pa_logo',
					has_variations: true,
					terms: [
						{
							id: 0,
							name: 'Yes',
							slug: 'Yes',
							default: true,
						},
						{
							id: 0,
							name: 'No',
							slug: 'No',
							default: false,
						},
					],
				},
			} );
		} );
	} );
	describe( 'Testing getVariationAttributes()', () => {
		it( 'returns empty object if there are no variations', () => {
			const variationAttributes = getVariationAttributes( null );
			expect( variationAttributes ).toStrictEqual( {} );
		} );
		it( 'returns list of attribute names and value pairs when given valid data', () => {
			const variationAttributes = getVariationAttributes( rawVariations );
			expect( variationAttributes ).toStrictEqual( {
				'id:35': {
					id: 35,
					attributes: {
						Color: 'blue',
						Logo: 'Yes',
					},
				},
				'id:28': {
					id: 28,
					attributes: {
						Color: 'red',
						Logo: 'No',
					},
				},
				'id:29': {
					id: 29,
					attributes: {
						Color: 'green',
						Logo: 'No',
					},
				},
				'id:30': {
					id: 30,
					attributes: {
						Color: 'blue',
						Logo: 'No',
					},
				},
			} );
		} );
	} );
	describe( 'Testing getVariationsMatchingSelectedAttributes()', () => {
		const attributes = getAttributes( rawAttributeData );
		const variationAttributes = getVariationAttributes( rawVariations );

		it( 'returns all variations, in the correct order, if no selections have been made yet', () => {
			const selectedAttributes = {};
			const matches = getVariationsMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( [ 35, 28, 29, 30 ] );
		} );

		it( 'returns correct subset of variations after a selection', () => {
			const selectedAttributes = {
				Color: 'blue',
			};
			const matches = getVariationsMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( [ 35, 30 ] );
		} );

		it( 'returns correct subset of variations after all selections', () => {
			const selectedAttributes = {
				Color: 'blue',
				Logo: 'No',
			};
			const matches = getVariationsMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( [ 30 ] );
		} );

		it( 'returns no results if selection does not match or is invalid', () => {
			const selectedAttributes = {
				Color: 'brown',
			};
			const matches = getVariationsMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( [] );
		} );
	} );
	describe( 'Testing getVariationMatchingSelectedAttributes()', () => {
		const attributes = getAttributes( rawAttributeData );
		const variationAttributes = getVariationAttributes( rawVariations );

		it( 'returns first match if no selections have been made yet', () => {
			const selectedAttributes = {};
			const matches = getVariationMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( 35 );
		} );

		it( 'returns first match after single selection', () => {
			const selectedAttributes = {
				Color: 'blue',
			};
			const matches = getVariationMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( 35 );
		} );

		it( 'returns correct match after all selections', () => {
			const selectedAttributes = {
				Color: 'blue',
				Logo: 'No',
			};
			const matches = getVariationMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( 30 );
		} );

		it( 'returns no match if invalid', () => {
			const selectedAttributes = {
				Color: 'brown',
			};
			const matches = getVariationMatchingSelectedAttributes(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( matches ).toStrictEqual( 0 );
		} );
	} );
	describe( 'Testing getActiveSelectControlOptions()', () => {
		const attributes = getAttributes( rawAttributeData );
		const variationAttributes = getVariationAttributes( rawVariations );

		it( 'returns all possible options if no selections have been made yet', () => {
			const selectedAttributes = {};
			const controlOptions = getActiveSelectControlOptions(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( controlOptions ).toStrictEqual( {
				Color: [
					{
						value: 'blue',
						label: 'Blue',
					},
					{
						value: 'green',
						label: 'Green',
					},
					{
						value: 'red',
						label: 'Red',
					},
				],
				Logo: [
					{
						value: 'Yes',
						label: 'Yes',
					},
					{
						value: 'No',
						label: 'No',
					},
				],
			} );
		} );

		it( 'returns only valid options if color is selected', () => {
			const selectedAttributes = {
				Color: 'green',
			};
			const controlOptions = getActiveSelectControlOptions(
				attributes,
				variationAttributes,
				selectedAttributes
			);
			expect( controlOptions ).toStrictEqual( {
				Color: [
					{
						value: 'blue',
						label: 'Blue',
					},
					{
						value: 'green',
						label: 'Green',
					},
					{
						value: 'red',
						label: 'Red',
					},
				],
				Logo: [
					{
						value: 'No',
						label: 'No',
					},
				],
			} );
		} );
	} );
	describe( 'Testing getDefaultAttributes()', () => {
		const defaultAttributes = getDefaultAttributes( formattedAttributes );

		it( 'should return default attributes in the format that is ready for setting state', () => {
			expect( defaultAttributes ).toStrictEqual( {
				Color: 'blue',
				Size: 'medium',
			} );
		} );

		it( 'should return an empty object if given unexpected values', () => {
			// @ts-expect-error Expected TS Error as we are checking how the function does with *unexpected values*.
			expect( getDefaultAttributes( [] ) ).toStrictEqual( {} );
			// @ts-expect-error Ditto above.
			expect( getDefaultAttributes( null ) ).toStrictEqual( {} );
			// @ts-expect-error Ditto above.
			expect( getDefaultAttributes( undefined ) ).toStrictEqual( {} );
		} );
	} );
} );
