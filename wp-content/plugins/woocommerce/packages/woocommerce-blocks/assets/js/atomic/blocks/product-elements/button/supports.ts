/* eslint-disable @wordpress/no-unsafe-wp-apis */
/**
 * External dependencies
 */
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import { __experimentalGetSpacingClassesAndStyles } from '@wordpress/block-editor';

export const supports = {
	...( isFeaturePluginBuild() && {
		color: {
			text: true,
			background: true,
			link: false,
			__experimentalSkipSerialization: true,
		},
		__experimentalBorder: {
			radius: true,
			__experimentalSkipSerialization: true,
		},
		...( typeof __experimentalGetSpacingClassesAndStyles === 'function' && {
			spacing: {
				margin: true,
				padding: true,
				__experimentalSkipSerialization: true,
			},
		} ),
		typography: {
			fontSize: true,
			lineHeight: true,
			__experimentalFontWeight: true,
			__experimentalFontFamily: true,
			__experimentalFontStyle: true,
			__experimentalTextTransform: true,
			__experimentalTextDecoration: true,
			__experimentalLetterSpacing: true,
			__experimentalDefaultControls: {
				fontSize: true,
			},
		},
		__experimentalSelector:
			'.wp-block-button.wc-block-components-product-button .wc-block-components-product-button__button',
	} ),
	...( typeof __experimentalGetSpacingClassesAndStyles === 'function' &&
		! isFeaturePluginBuild() && {
			spacing: {
				margin: true,
			},
		} ),
};
