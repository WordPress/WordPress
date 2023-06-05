/**
 * External dependencies
 */
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import {
	// @ts-expect-error We check if this exists before using it.
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalGetSpacingClassesAndStyles,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import sharedConfig from '../shared/config';

export const supports = {
	...sharedConfig.supports,
	color: {
		text: true,
		background: true,
	},
	typography: {
		fontSize: true,
		lineHeight: true,
		...( isFeaturePluginBuild() && {
			__experimentalFontWeight: true,
			__experimentalFontFamily: true,
			__experimentalFontStyle: true,
			__experimentalTextTransform: true,
			__experimentalTextDecoration: true,
			__experimentalLetterSpacing: true,
		} ),
	},
	...( typeof __experimentalGetSpacingClassesAndStyles === 'function' && {
		spacing: {
			margin: true,
			padding: true,
		},
	} ),
};
