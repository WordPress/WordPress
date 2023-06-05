/**
 * Internal dependencies
 */
import {
	getCartData,
	getCartTotals,
	getCartMeta,
	getCartErrors,
	isApplyingCoupon,
	getCouponBeingApplied,
	isRemovingCoupon,
	getCouponBeingRemoved,
} from '../selectors';

const state = {
	cartData: {
		coupons: [
			{
				code: 'test',
				totals: {
					currency_code: 'GBP',
					currency_symbol: '£',
					currency_minor_unit: 2,
					currency_decimal_separator: '.',
					currency_thousand_separator: ',',
					currency_prefix: '£',
					currency_suffix: '',
					total_discount: '583',
					total_discount_tax: '117',
				},
			},
		],
		items: [
			{
				key: '1f0e3dad99908345f7439f8ffabdffc4',
				id: 19,
				quantity: 1,
				name: 'Album',
				short_description: '<p>This is a simple, virtual product.</p>',
				description:
					'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt. Donec ut metus leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed luctus, dui eu sagittis sodales, nulla nibh sagittis augue, vel porttitor diam enim non metus. Vestibulum aliquam augue neque. Phasellus tincidunt odio eget ullamcorper efficitur. Cras placerat ut turpis pellentesque vulputate. Nam sed consequat tortor. Curabitur finibus sapien dolor. Ut eleifend tellus nec erat pulvinar dignissim. Nam non arcu purus. Vivamus et massa massa.</p>',
				sku: 'woo-album',
				low_stock_remaining: null,
				permalink: 'http://local.wordpress.test/product/album/',
				images: [
					{
						id: 48,
						src: 'http://local.wordpress.test/wp-content/uploads/2019/12/album-1.jpg',
						thumbnail:
							'http://local.wordpress.test/wp-content/uploads/2019/12/album-1-324x324.jpg',
						srcset: 'http://local.wordpress.test/wp-content/uploads/2019/12/album-1.jpg 800w, http://local.wordpress.test/wp-content/uploads/2019/12/album-1-324x324.jpg 324w, http://local.wordpress.test/wp-content/uploads/2019/12/album-1-100x100.jpg 100w, http://local.wordpress.test/wp-content/uploads/2019/12/album-1-416x416.jpg 416w, http://local.wordpress.test/wp-content/uploads/2019/12/album-1-300x300.jpg 300w, http://local.wordpress.test/wp-content/uploads/2019/12/album-1-150x150.jpg 150w, http://local.wordpress.test/wp-content/uploads/2019/12/album-1-768x768.jpg 768w',
						sizes: '(max-width: 800px) 100vw, 800px',
						name: 'album-1.jpg',
						alt: '',
					},
				],
				variation: [],
				totals: {
					currency_code: 'GBP',
					currency_symbol: '£',
					currency_minor_unit: 2,
					currency_decimal_separator: '.',
					currency_thousand_separator: ',',
					currency_prefix: '£',
					currency_suffix: '',
					line_subtotal: '1250',
					line_subtotal_tax: '250',
					line_total: '1000',
					line_total_tax: '200',
				},
			},
			{
				key: '6512bd43d9caa6e02c990b0a82652dca',
				id: 11,
				quantity: 1,
				name: 'Beanie',
				short_description: '<p>This is a simple product.</p>',
				description:
					'<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>',
				sku: 'woo-beanie',
				low_stock_remaining: null,
				permalink: 'http://local.wordpress.test/product/beanie/',
				images: [
					{
						id: 40,
						src: 'http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2.jpg',
						thumbnail:
							'http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-324x324.jpg',
						srcset: 'http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2.jpg 801w, http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-324x324.jpg 324w, http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-100x100.jpg 100w, http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-416x416.jpg 416w, http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-300x300.jpg 300w, http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-150x150.jpg 150w, http://local.wordpress.test/wp-content/uploads/2019/12/beanie-2-768x768.jpg 768w',
						sizes: '(max-width: 801px) 100vw, 801px',
						name: 'beanie-2.jpg',
						alt: '',
					},
				],
				variation: [],
				totals: {
					currency_code: 'GBP',
					currency_symbol: '£',
					currency_minor_unit: 2,
					currency_decimal_separator: '.',
					currency_thousand_separator: ',',
					currency_prefix: '£',
					currency_suffix: '',
					line_subtotal: '1667',
					line_subtotal_tax: '333',
					line_total: '1333',
					line_total_tax: '267',
				},
			},
		],
		items_count: 2,
		items_weight: 0,
		needs_payment: true,
		needs_shipping: true,
		totals: {
			currency_code: 'GBP',
			currency_symbol: '£',
			currency_minor_unit: 2,
			currency_decimal_separator: '.',
			currency_thousand_separator: ',',
			currency_prefix: '£',
			currency_suffix: '',
			total_items: '2917',
			total_items_tax: '583',
			total_fees: '0',
			total_fees_tax: '0',
			total_discount: '583',
			total_discount_tax: '117',
			total_shipping: '2000',
			total_shipping_tax: '400',
			total_price: '5200',
			total_tax: '867',
			tax_lines: [
				{
					name: 'Tax',
					price: '867',
				},
			],
		},
	},
	metaData: {
		applyingCoupon: 'test-coupon',
		removingCoupon: 'test-coupon2',
	},
	errors: [
		{
			code: '100',
			message: 'Test Error',
			data: {},
		},
	],
};

describe( 'getCartData', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( getCartData( state ) ).toEqual( state.cartData );
	} );
} );

describe( 'getCartTotals', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( getCartTotals( state ) ).toEqual( state.cartData.totals );
	} );
} );

describe( 'getCartMeta', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( getCartMeta( state ) ).toEqual( state.metaData );
	} );
} );

describe( 'getCartErrors', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( getCartErrors( state ) ).toEqual( state.errors );
	} );
} );

describe( 'isApplyingCoupon', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( isApplyingCoupon( state ) ).toEqual( true );
	} );
} );

describe( 'getCouponBeingApplied', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( getCouponBeingApplied( state ) ).toEqual(
			state.metaData.applyingCoupon
		);
	} );
} );

describe( 'isRemovingCoupon', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( isRemovingCoupon( state ) ).toEqual( true );
	} );
} );

describe( 'getCouponBeingRemoved', () => {
	it( 'returns expected values for items existing in state', () => {
		expect( getCouponBeingRemoved( state ) ).toEqual(
			state.metaData.removingCoupon
		);
	} );
} );
