/**
 * External dependencies
 */
import { isFeaturePluginBuild } from '@woocommerce/block-settings';

export const supports = {
	...( isFeaturePluginBuild() && {
		color: {
			background: false,
		},
		typography: {
			fontSize: true,
		},
		__experimentalSelector: '.wc-block-components-product-summary',
	} ),
};
