/**
 * External dependencies
 */
import { isFeaturePluginBuild } from '@woocommerce/block-settings';

export const supports = {
	...( isFeaturePluginBuild() && {
		color: {
			text: true,
			background: false,
			link: false,
		},
		typography: {
			fontSize: true,
		},
		__experimentalSelector: '.wc-block-components-product-stock-indicator',
	} ),
};
