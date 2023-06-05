/**
 * External dependencies
 */
import { registerBlockVariation } from '@wordpress/blocks';

interface VariationDetails {
	blockDescription: string;
	blockIcon: JSX.Element;
	blockTitle: string;
	variationName: string;
}

export function registerElementVariation(
	coreName: string,
	{ blockDescription, blockIcon, blockTitle, variationName }: VariationDetails
) {
	registerBlockVariation( coreName, {
		description: blockDescription,
		name: variationName,
		title: blockTitle,
		isActive: ( blockAttributes ) =>
			blockAttributes.__woocommerceNamespace === variationName,
		icon: {
			src: blockIcon,
		},
		attributes: {
			__woocommerceNamespace: variationName,
		},
		scope: [ 'block', 'inserter' ],
	} );
}
