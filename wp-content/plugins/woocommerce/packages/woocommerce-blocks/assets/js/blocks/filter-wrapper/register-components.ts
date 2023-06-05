/**
 * External dependencies
 */
import { registerBlockComponent } from '@woocommerce/blocks-registry';
import { lazy } from '@wordpress/element';
import { WC_BLOCKS_BUILD_URL } from '@woocommerce/block-settings';

// Modify webpack publicPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = WC_BLOCKS_BUILD_URL;

registerBlockComponent( {
	blockName: 'woocommerce/active-filters',
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "active-filters-wrapper" */
				'../active-filters/block-wrapper'
			)
	),
} );

registerBlockComponent( {
	blockName: 'woocommerce/price-filter',
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "price-filter-wrapper" */
				'../price-filter/block-wrapper'
			)
	),
} );

registerBlockComponent( {
	blockName: 'woocommerce/stock-filter',
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "stock-filter-wrapper" */
				'../stock-filter/block-wrapper'
			)
	),
} );

registerBlockComponent( {
	blockName: 'woocommerce/attribute-filter',
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "attribute-filter-wrapper" */
				'../attribute-filter/block-wrapper'
			)
	),
} );

registerBlockComponent( {
	blockName: 'woocommerce/rating-filter',
	component: lazy(
		() =>
			import(
				/* webpackChunkName: "rating-filter-wrapper" */
				'../rating-filter/block-wrapper'
			)
	),
} );
