/* eslint-disable @wordpress/no-unsafe-wp-apis */
/**
 * External dependencies
 */
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import { __experimentalGetSpacingClassesAndStyles } from '@wordpress/block-editor';

export const supports = {
	html: false,
	...( isFeaturePluginBuild() && {
		color: {
			gradients: true,
			background: true,
			link: false,
			__experimentalSkipSerialization: true,
		},
		typography: {
			fontSize: true,
			lineHeight: true,
			__experimentalFontFamily: true,
			__experimentalFontWeight: true,
			__experimentalFontStyle: true,
			__experimentalSkipSerialization: true,
			__experimentalLetterSpacing: true,
			__experimentalTextTransform: true,
			__experimentalTextDecoration: true,
		},
		__experimentalBorder: {
			color: true,
			radius: true,
			width: true,
			__experimentalSkipSerialization: true,
		},
		...( typeof __experimentalGetSpacingClassesAndStyles === 'function' && {
			spacing: {
				margin: true,
				padding: true,
			},
		} ),
		__experimentalSelector: '.wc-block-components-product-sale-badge',
	} ),
	...( typeof __experimentalGetSpacingClassesAndStyles === 'function' &&
		! isFeaturePluginBuild() && {
			spacing: {
				margin: true,
			},
		} ),
};
