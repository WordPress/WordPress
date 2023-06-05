/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

const attributes = ( {
	defaultTitle = __( 'Step', 'woo-gutenberg-products-block' ),
	defaultDescription = __(
		'Step description text.',
		'woo-gutenberg-products-block'
	),
	defaultShowStepNumber = true,
}: {
	defaultTitle: string;
	defaultDescription: string;
	defaultShowStepNumber?: boolean;
} ): Record< string, Record< string, unknown > > => ( {
	title: {
		type: 'string',
		default: defaultTitle,
	},
	description: {
		type: 'string',
		default: defaultDescription,
	},
	showStepNumber: {
		type: 'boolean',
		default: defaultShowStepNumber,
	},
} );

export default attributes;
