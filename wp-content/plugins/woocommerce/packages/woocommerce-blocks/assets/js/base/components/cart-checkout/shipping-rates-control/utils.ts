/**
 * External dependencies
 */
import { _n, sprintf } from '@wordpress/i18n';
import { speak } from '@wordpress/a11y';

export const speakFoundShippingOptions = (
	packageCount: number,
	rateCount: number
) => {
	if ( packageCount === 1 ) {
		speak(
			sprintf(
				/* translators: %d number of shipping options found. */
				_n(
					'%d shipping option was found.',
					'%d shipping options were found.',
					rateCount,
					'woo-gutenberg-products-block'
				),
				rateCount
			)
		);
	} else {
		speak(
			sprintf(
				/* translators: %d number of shipping packages packages. */
				_n(
					'Shipping option searched for %d package.',
					'Shipping options searched for %d packages.',
					packageCount,
					'woo-gutenberg-products-block'
				),
				packageCount
			) +
				' ' +
				sprintf(
					/* translators: %d number of shipping options available. */
					_n(
						'%d shipping option was found',
						'%d shipping options were found',
						rateCount,
						'woo-gutenberg-products-block'
					),
					rateCount
				)
		);
	}
};
